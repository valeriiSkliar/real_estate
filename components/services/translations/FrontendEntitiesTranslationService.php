<?php

namespace app\components\services\translations;

use app\helpers\ErrorLogHelper;
use app\helpers\FileHelper;
use app\models\Languages;
use Yii;
use yii\web\NotFoundHttpException;


class FrontendEntitiesTranslationService
{
    private TranslationService $translationService;
    private $translationPath;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
        $this->translationPath = Yii::getAlias('@webRoot/uploads/translation/');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function translateEntities($sourceLanguage, $targetLanguage = null): int
    {
        $entitiesList = [
            'site' =>$this->getDefaultLanguageTranslations($sourceLanguage),
            'auth' => $this->getDefaultAuthLanguageTranslations($sourceLanguage),
        ];

        $count = 0;

        foreach ($entitiesList as $key => $file) {
            $count = ($targetLanguage)
                ? $this->translate($targetLanguage, $sourceLanguage, $file, $key)
                : $this->translateToAllLanguages($sourceLanguage, $file, $key);
        }

        return $count;
    }

    /**
     * @throws NotFoundHttpException
     */
    private function getDefaultLanguageTranslations($sourceLanguage): array
    {
        $filePath = $this->translationPath . $sourceLanguage . '.json';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Языковой файл $sourceLanguage не найден.");
        }

        return json_decode(file_get_contents($filePath), true);
    }

    private function getDefaultAuthLanguageTranslations($sourceLanguage): array
    {
        $filePath = $this->translationPath . 'auth/' . $sourceLanguage . '.json';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Языковой файл $sourceLanguage не найден.");
        }

        return json_decode(file_get_contents($filePath), true);
    }

    private function translateToAllLanguages($sourceLanguage, $translation, $key): int
    {
        ErrorLogHelper::logTranslationInfo("Идет перевод на все языки");

        $languages = Languages::getAllLanguages();

        $count = 0;

        foreach ($languages as $language) {
            ErrorLogHelper::logTranslationInfo("Идет перевод на язык $language->name");

            if ($language->slug === $sourceLanguage) {
                continue;
            }

            $count += $this->translate($language->slug, $sourceLanguage, $translation, $key);

            ErrorLogHelper::logTranslationInfo("Перевели на язык $language->name, сохранено: $count записей");
        }

        return $count;
    }

    private function translate($targetLanguage, $sourceLanguage, $translation, $key): int
    {
        ErrorLogHelper::logTranslationInfo("Идет перевод фронтенда на целевой язык: $targetLanguage");

        $count = 0;
        $existsInLanguages = $this->getExistingLanguages($key);
        $flatTranslations = $this->flattenTranslations($translation);

        if ($targetLanguage === $sourceLanguage || $existsInLanguages && in_array($targetLanguage, $existsInLanguages)) {
            ErrorLogHelper::logTranslationInfo(
                "На данном языке: $targetLanguage уже есть переводы. Не требуется переводить."
            );

            return $count;
        }

        $this->translationService->setTargetLanguage($targetLanguage);
        $translatedNames = $this->translationService->translateBatchInChunks($flatTranslations);

        if (!$translatedNames || count($translatedNames)!== count($flatTranslations)) {
            ErrorLogHelper::logTranslationInfo(
                "Ошибка. Не удалось получить переводы."
            );

            return $count;
        }

        $nestedTranslations = $this->unflattenTranslations(array_combine(array_keys($flatTranslations), $translatedNames));

        if ($this->addToFile($targetLanguage, $nestedTranslations, $key)){
            ErrorLogHelper::logTranslationInfo(
                "Файл перевода $targetLanguage.json создан успешно."
            );

            ++$count;
        }

        return $count;
    }

    private function getExistingLanguages($key): array
    {
        $pathSuffix = $key === 'auth' ? 'auth/' : '';
        $files = glob($this->translationPath . $pathSuffix . '*.json');

        return array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $files);
    }

    private function flattenTranslations(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenTranslations($value, $fullKey));
            } else {
                $result[$fullKey] = $value;
            }
        }
        return $result;
    }

    private function unflattenTranslations(array $flatArray): array
    {
        $result = [];
        foreach ($flatArray as $key => $value) {
            $keys = explode('.', $key);
            $temp = &$result;
            foreach ($keys as $innerKey) {
                if (!isset($temp[$innerKey])) {
                    $temp[$innerKey] = [];
                }
                $temp = &$temp[$innerKey];
            }
            $temp = $value;
        }
        return $result;
    }

    private function addToFile($targetLanguage, $nestedTranslations, $key): bool|int
    {
        $pathSuffix = $key === 'auth' ? 'auth/' : '';
        $path = $this->translationPath . $pathSuffix;
        if (!is_dir(dirname($path))) {
            FileHelper::makeDirectory($path);
        }

        $filePath = Yii::getAlias($path . $targetLanguage . '.json');

       return FileHelper::createFile(
           $filePath,
           json_encode($nestedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
       );
    }
}