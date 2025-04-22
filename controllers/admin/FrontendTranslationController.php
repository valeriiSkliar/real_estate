<?php

namespace app\controllers\admin;

use app\components\services\translations\FrontendEntitiesTranslationService;
use app\components\services\translations\TranslationService;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FrontendTranslationController extends AdminController
{
    private $translationPath;
    private FrontendEntitiesTranslationService $frontendEntitiesTranslationService;

    public function init(): void
    {
        parent::init();
        $this->translationPath = Yii::getAlias('@webRoot/uploads/translation/');
        $this->frontendEntitiesTranslationService = new FrontendEntitiesTranslationService(
            new TranslationService(TranslationService::DEFAULT_LANGUAGE)
        );
    }

    /**
     * Метод для отображения списка доступных языков.
     */
    public function actionIndex()
    {
        //Переводы сайта
        $siteFiles = glob($this->translationPath . '*.json');

        $siteLanguages = array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $siteFiles);

        //Переводы приложения авторизации
        $authFiles = glob($this->translationPath . 'auth/' . '*.json');

        $authLanguages = array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $authFiles);

        return $this->render('index', ['siteLanguages' => $siteLanguages, 'authLanguages' => $authLanguages]);
    }

    /**
     * Метод для редактирования конкретного языкового файла.
     */
    public function actionEdit($lang, $auth = false)
    {
        $pathSuffix = $auth ? 'auth/' : '';
        $filePath = $this->translationPath . $pathSuffix . $lang . '.json';

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException("Языковой файл $lang не найден.");
        }

        $translations = json_decode(file_get_contents($filePath), true);

        if (Yii::$app->request->isPost) {
            $updatedTranslations = Yii::$app->request->post('translations');

            // Преобразование ключей с вложенностью обратно в массив
            $finalTranslations = [];
            foreach ($updatedTranslations as $key => $value) {
                $keys = explode('[', str_replace(']', '', $key));
                $temp = &$finalTranslations;

                foreach ($keys as $innerKey) {
                    if (!isset($temp[$innerKey])) {
                        $temp[$innerKey] = [];
                    }
                    $temp = &$temp[$innerKey];
                }
                $temp = $value;
            }

            file_put_contents($filePath, json_encode($finalTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            Yii::$app->session->setFlash('success', "Файл $lang успешно обновлен.");

            return $this->redirect(['edit', 'lang' => $lang]);
        }

        return $this->render('edit', [
            'lang' => $lang,
            'translations' => $translations,
        ]);
    }

    /**
     * Метод для создания нового языкового файла и его редактирования.
     * @throws NotFoundHttpException
     */
    public function actionCreate($newLang, $auth = false)
    {
        $pathSuffix = $auth ? 'auth/' : '';
        $sourceFile = $this->translationPath. $pathSuffix  . 'en.json';
        $newFile = $this->translationPath. $pathSuffix  . $newLang . '.json';

        if (!file_exists($sourceFile)) {
            throw new \yii\web\ServerErrorHttpException("Исходный файл en.json не найден.");
        }

        if (file_exists($newFile)) {
            Yii::$app->session->setFlash('error', "Файл для языка $newLang уже существует.");
            return $this->redirect(['index']);
        }

        $translated = $this->frontendEntitiesTranslationService->translateEntities(
            TranslationService::DEFAULT_LANGUAGE,
            $newLang
        );

        if ($translated) {
            Yii::$app->session->setFlash('success', "Файл с переводами для языка $newLang создан.");

            return $this->redirect(['edit', 'lang' => $newLang, 'auth' => $auth]);
        }

        Yii::$app->session->setFlash('error', "Файл для языка $newLang Не создан.");

        return $this->redirect(['index']);
    }
}