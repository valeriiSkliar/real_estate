<?php

namespace app\components\services\translations;

use app\helpers\ErrorLogHelper;
use app\models\Buttons;
use app\models\Disease;
use app\models\Languages;
use app\models\Oils;
use app\models\Texts;
use app\models\Topics;

class MainEntitiesTranslationService
{
    private TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function translateEntities($sourceLanguage, $targetLanguage = null): int
    {
        $entitiesList = [
            $this->getOils($sourceLanguage),
            $this->getDiseases($sourceLanguage),
        ];

        $count = 0;

        foreach ($entitiesList as $models) {
            $count = ($targetLanguage)
                ? $this->translate($targetLanguage, $models)
                : $this->translateToAllLanguages($models);
        }

        return $count;
    }

    private function getOils($language): array
    {
        return Oils::find()
            ->where(['language' => $language])
            ->all();
    }

    private function getDiseases($language): array
    {
        return Disease::find()
            ->where(['language' => $language])
            ->all();
    }

    private function translateToAllLanguages($entities): int
    {
        $count = 0;
        foreach ($entities as $entity) {
            if ($this->translationService->translateMainEntityToAllLanguages($entity)) {
                ++$count;
            }
        }

        return $count;
    }

    private function translate($targetLanguage, array $entities): int
    {
        $count = 0;

        foreach ($entities as $existingModel) {
            $newModel = clone $existingModel;
            $class = get_class($newModel);
            $newModel->isNewRecord = true;
            $newModel->id = null;

            $this->translationService->setTargetLanguage($targetLanguage);

            if ($this->translationService->translateMainEntity($targetLanguage, $existingModel, $newModel)){
                ErrorLogHelper::logTranslationInfo(
                    "Сохранено: $class с именем: {$newModel->name}, id={$newModel->id} на язык $targetLanguage"
                );

                $count++;
            }else {
                ErrorLogHelper::logTranslationInfo(
                    "Не удалось сохранить. Ошибки: ". print_r($newModel->getErrors(), true),
                    "Сущность $class с id={$existingModel->id} на язык $targetLanguage"
                );
            }
        }

        return $count;

    }
}