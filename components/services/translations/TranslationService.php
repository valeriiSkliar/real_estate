<?php

namespace app\components\services\translations;

use app\enums\TopicTypes;
use app\helpers\DescriptionParsingHelper;
use app\helpers\ErrorLogHelper;
use app\models\Languages;
use app\models\Oils;
use app\models\Topics;
use app\components\translations\interfaces\TranslatorInterface;
use app\components\translations\providers\TranslatorFactory;
use Google\Cloud\Core\Exception\GoogleException;

class TranslationService
{
    public const DEFAULT_LANGUAGE = 'en';
    private string $targetLanguage;
    private TranslatorInterface $translator;

    /**
     * @throws GoogleException
     */
    public function __construct($targetLanguage)
    {
        $this->translator = TranslatorFactory::createTranslator();
        $this->targetLanguage = $targetLanguage;
        $this->translator->setTargetLanguage($targetLanguage);
    }

    public function setTargetLanguage($targetLanguage): void
    {
        $this->translator->setTargetLanguage($targetLanguage);
        $this->targetLanguage = $targetLanguage;
    }

    public function translate($text)
    {
        if (empty($text) || empty($this->translator->getTargetLanguage())) {
            $this->logError($text);

            return null;
        }

        $translatedContent = $this->translator->translate($text);

        if (is_object($translatedContent)) {
            $this->logError($translatedContent);
        }

        return $translatedContent;
    }

    public function translateBatch($texts)
    {
        if (empty($texts) || empty($this->translator->getTargetLanguage())) {
            $this->logError($texts);

            return null;
        }

        $translatedContent = $this->translator->translateBatch($texts);

        if (is_object($translatedContent)) {
            $this->logError($translatedContent);
        }

        return $translatedContent;
    }

    public function logError($translatedContent): void
    {
        if (empty($translatedContent)) {
            ErrorLogHelper::logTranslationInfo(
                $translatedContent,
                "Нечего переводить или не задан язык перевода"
            );

            return;
        }

        if (is_object($translatedContent)){
            ErrorLogHelper::logTranslationInfo(
                $translatedContent,
                "Получили ошибку при переводе"
            );

            return;
        }
    }

    public function translateBatchInChunks(array $names): array
    {

        $chunkSize = 128; // Максимально допустимое количество сегментов
        $translatedNames = [];

        // Разбиваем массив на части и переводим каждую часть отдельно
        foreach (array_chunk($names, $chunkSize) as $chunk) {
            $response = $this->translateBatch($chunk);
            if ($response !== null) {
                if (is_object($response)) {
                    return [];
                }

                $translatedNames = array_merge($translatedNames, $response);
            }
        }

        return $translatedNames;
    }

    public function translateParsedDescription(int $type, ?string $description, string $originalLanguage, $id): array
    {
        $parsed = [];

        if (!$description) {
            return $parsed;
        }

        // Получаем все темы, связанные с описанием
        $topics = Topics::getAllIndexedBySlug($type, $originalLanguage);
        $parsed = DescriptionParsingHelper::getParsedDescription($topics, $description);

        // Извлекаем все текстовые фрагменты для перевода
        $contentsToTranslate = array_values($parsed);

        // Выполняем перевод всех фрагментов одним запросом
        $translatedContents = $this->translateBatch($contentsToTranslate);

        // Назначаем переведенные фрагменты обратно к их соответствующим ключам
        $index = 0;
        foreach ($parsed as $slug => $content) {
            if (is_object($translatedContents)){
                ErrorLogHelper::logTranslationInfo(
                    $translatedContents,
                    "Получили ошибку при переводе описания для темы $slug, id={$id}"
                );

                $parsed[$slug] = '';
                $index++;

                continue;
            }

            $parsed[$slug] = $translatedContents[$index] ?? $content;
            $index++;
        }

        return $parsed;
    }

    public function translateMainEntity($language, $existingModel, $newModel): bool
    {
        $newModel->attributes = $existingModel->attributes;
        $newModel->language = $language;

        if ($existingModel->language !== $language) {
            $type = ($existingModel instanceof Oils) ? TopicTypes::OIL->value : TopicTypes::DISEASE->value;
            $class = get_class($existingModel);
            ErrorLogHelper::logTranslationInfo(
                "Идет перевод для сущности $class с id={$existingModel->id} на язык $language"
            );
            // Переводим description с разбором
            $newModel->description_parts = $this->translateParsedDescription(
                $type,
                $existingModel->description,
                $existingModel->language,
                $existingModel->id
            );

            // Переводим все остальные поля одним запросом
            $fieldsToTranslate = [
                $existingModel->name,
                $existingModel->keywords,
                $existingModel->meta_title,
                $existingModel->meta_description
            ];

            $translatedFields = $this->translateBatch($fieldsToTranslate);

            // Назначаем переведенные значения
            if ($type !== TopicTypes::OIL->value) {
                $newModel->name = $translatedFields[0] ?? null;
            }

            $newModel->keywords = $translatedFields[1] ?? null;
            $newModel->meta_title = $translatedFields[2] ?? null;
            $newModel->meta_description = $translatedFields[3] ?? null;
        }else{
            ErrorLogHelper::logTranslationInfo(
                "Целевой язык совпадает с языком оригинала для сущности ". get_class($existingModel). " с id={$existingModel->id}"
            );
        }

        if ($newModel->save()) {
            return true;
        }else {
            ErrorLogHelper::logTranslationInfo(
                $newModel->errors,
                "Ошибка сохранения перевода для сущности ". get_class($existingModel). " с id={$existingModel->id}"
            );
        }

        return false;
    }

    public function translateMainEntityToAllLanguages($existingModel): int
    {
        $languages = Languages::getAllLanguages();
        $existsInLanguages = $existingModel->getTranslatedEntities()->select(['language'])->column();
        $count = 0;

        foreach ($languages as $language) {
            if ($language->slug === $existingModel->language || $existsInLanguages && in_array($language->slug, $existsInLanguages)) {
                continue;
            }

            ErrorLogHelper::logTranslationInfo("Идет перевод на язык $language->name");

            $newModel = clone $existingModel;
            $class = get_class($newModel);
            $newModel->isNewRecord = true;
            $newModel->id = null;
            $this->setTargetLanguage($language->slug);

            if ($this->translateMainEntity($language->slug, $existingModel, $newModel)){
                ErrorLogHelper::logTranslationInfo(
                    "Сохранено: $class с id={$newModel->id} на язык $language->slug"
                );

                $count++;
            }else {
                ErrorLogHelper::logTranslationInfo(
                    "Не удалось сохранить. Ошибки: ". print_r($newModel->getErrors(), true),
                    "Сущность $class с id={$existingModel->id} на язык $language->slug"
                );
            }

        }

        return $count;
    }
}
