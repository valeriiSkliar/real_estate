<?php

namespace app\components\services\translations;

use app\helpers\ErrorLogHelper;
use app\models\Buttons;
use app\models\Languages;
use app\models\Texts;
use app\models\Topics;
use yii\db\ActiveRecord;

class SimpleEntitiesTranslationService
{
    private TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function translateEntities($sourceLanguage, $targetLanguage = null): int
    {
        $entitiesList = [
            $this->getButtons($sourceLanguage),
            $this->getTexts($sourceLanguage),
            $this->getTopics($sourceLanguage)
        ];

        $count = 0;

        foreach ($entitiesList as $models) {
            $count = ($targetLanguage)
                ? $this->translate($targetLanguage, $sourceLanguage, $models)
                : $this->translateToAllLanguages($sourceLanguage, $models);
        }

        return $count;
    }

    private function getButtons($sourceLanguage): array
    {
        return Buttons::find()
            ->where(['language' => $sourceLanguage])
            ->andFilterWhere(['not like','slug', 'language'])
            ->all();
    }

    private function getTexts($sourceLanguage): array
    {
        return Texts::find()
            ->where(['language' => $sourceLanguage])
            ->all();
    }

    private function getTopics($sourceLanguage): array
    {
        return Topics::find()
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
        $existsInLanguages = $this->getExistedLanguages($entities);
        $originalNames = $this->getOriginalNames($entities);

        if ($targetLanguage === $sourceLanguage || $existsInLanguages && in_array($targetLanguage, $existsInLanguages)) {
            ErrorLogHelper::logTranslationInfo(
                "На данном языке: $targetLanguage уже есть переводы. Не требуется переводить."
            );

            return $count;
        }

        $this->translationService->setTargetLanguage($targetLanguage);
        $translatedNames = $this->translationService->translateBatch($originalNames);

        foreach ($entities as $existingModel) {
            $newModel = clone $existingModel;
            $class = get_class($newModel);
            $newModel->isNewRecord = true;
            $newModel->id = null;
            $newModel->language = $targetLanguage;
            $newModel->name = $translatedNames[$count] ?? null;

            if ($newModel->name && $newModel->save()) {
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

    private function getExistedLanguages($entities): array
    {
        $firstElement = reset($entities);

        return $firstElement->getTranslatedEntities()->select(['language'])->column();
    }

    private function getOriginalNames($entities): array
    {
        $originalNames = [];

        foreach ($entities as $existingModel) {
            $originalNames[] = $existingModel->name;
        }

        return $originalNames;
    }
}