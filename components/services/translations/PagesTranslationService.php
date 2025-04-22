<?php

namespace app\components\services\translations;

use app\helpers\ErrorLogHelper;
use app\models\Buttons;
use app\models\Languages;
use app\models\Pages;
use app\models\Texts;
use app\models\Topics;
use yii\db\ActiveRecord;

class PagesTranslationService
{
    private TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function translateEntities($sourceLanguage, $targetLanguage = null): int
    {
        $entitiesList = [
            $this->getPages($sourceLanguage),
        ];

        $count = 0;

        foreach ($entitiesList as $models) {
            $count = ($targetLanguage)
                ? $this->translate($targetLanguage, $sourceLanguage, $models)
                : $this->translateToAllLanguages($sourceLanguage, $models);
        }

        return $count;
    }

    private function getPages($sourceLanguage): array
    {
        return Pages::find()
            ->where(['language' => $sourceLanguage])
            ->all();
    }

    private function translateToAllLanguages($sourceLanguage,$entities): int
    {
        ErrorLogHelper::logTranslationInfo("Идет перевод на все языки");

        $languages = Languages::getAllLanguages();

        $count = 0;

        foreach ($languages as $language) {
            ErrorLogHelper::logTranslationInfo("Идет перевод на язык $language->name");

            if ($language->slug === $sourceLanguage) {
                continue;
            }

            $count += $this->translate($language->slug, $sourceLanguage, $entities);

            ErrorLogHelper::logTranslationInfo("Перевели на язык $language->name, сохранено: $count записей");
        }

        return $count;
    }

    private function translate($targetLanguage, $sourceLanguage, $entities): int
    {
        ErrorLogHelper::logTranslationInfo("Идет перевод на целевой язык: $targetLanguage");

        $count = 0;

        /** @var Pages $existingModel */
        foreach ($entities as $existingModel) {
            $existsInLanguages = $this->getExistedLanguages($existingModel);

            if ($targetLanguage === $sourceLanguage || $existsInLanguages && in_array($targetLanguage, $existsInLanguages)) {
                ErrorLogHelper::logTranslationInfo(
                    "На данном языке: $targetLanguage уже есть переводы. Не требуется переводить."
                );

                continue;
            }

            $newModel = clone $existingModel;
            $class = get_class($newModel);
            $newModel->isNewRecord = true;
            $newModel->id = null;
            $newModel->language = $targetLanguage;

            $fieldsToTranslate = [
                $existingModel->h1,
                $existingModel->meta_keywords,
                $existingModel->meta_title,
                $existingModel->meta_description,
                $existingModel->text
            ];

            $this->translationService->setTargetLanguage($targetLanguage);
            $translatedFields = $this->translationService->translateBatch($fieldsToTranslate);

            // Назначаем переведенные значения
            $newModel->h1 = $translatedFields[0] ?? null;
            $newModel->meta_keywords = $translatedFields[1] ?? null;
            $newModel->meta_title = $translatedFields[2] ?? null;
            $newModel->meta_description = $translatedFields[3] ?? null;
            $newModel->text = $translatedFields[4] ?? null;

            if ($newModel->h1 && $newModel->save()) {
                ErrorLogHelper::logTranslationInfo(
                    "Сохранено: $class с id={$newModel->id} на язык $targetLanguage"
                );

                ++$count;
            }else {
                ErrorLogHelper::logTranslationInfo(
                    "Не удалось сохранить. Ошибки: ". print_r($newModel->getErrors(), true),
                    "Сущность $class с id={$existingModel->id} на язык $targetLanguage"
                 );
            }
        }

        return $count;
    }

    private function getExistedLanguages($entity): array
    {

        return $entity->getTranslatedEntities()->select(['language'])->column();
    }
}