<?php

namespace app\commands;

use app\components\services\RecordsTransferService;
use app\components\services\translations\FrontendEntitiesTranslationService;
use app\components\services\translations\MainEntitiesTranslationService;
use app\components\services\translations\PagesTranslationService;
use app\components\services\translations\SimpleEntitiesTranslationService;
use app\components\services\translations\TranslationService;
use app\helpers\ErrorLogHelper;
use app\models\Buttons;
use app\models\Languages;
use yii\console\Controller;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class MultilanguageController extends Controller
{
    public TranslationService $translationService;
    public SimpleEntitiesTranslationService $simpleEntitiesTranslationService;
    public MainEntitiesTranslationService $mainEntitiesTranslationService;
    public FrontendEntitiesTranslationService $frontendEntitiesTranslationService;
    public PagesTranslationService $pagesTranslationService;

    public function __construct($id, $module, $config = [])
    {
        $this->translationService = new TranslationService(TranslationService::DEFAULT_LANGUAGE);
        $this->simpleEntitiesTranslationService = new SimpleEntitiesTranslationService($this->translationService);
        $this->mainEntitiesTranslationService = new MainEntitiesTranslationService($this->translationService);
        $this->frontendEntitiesTranslationService = new FrontendEntitiesTranslationService($this->translationService);
        $this->pagesTranslationService = new PagesTranslationService($this->translationService);

        parent::__construct($id, $module, $config);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionGenerateCopiesForNewLanguage($language): bool
    {
        ErrorLogHelper::logTranslationInfo("Начинаем копирование для нового языка: $language");

        $model = Languages::findOne(['slug' => $language]);

        if (!$model) {
            return false;
        }

        ErrorLogHelper::logTranslationInfo("Создаем языковые кнопки в бот для нового языка: $language");
        //Создаем кнопку для нового языка в боте
        Buttons::createNewLanguageButton($model);

        ErrorLogHelper::logTranslationInfo("Переводим кнопки, тексты, темы");
        //Переводим кнопки, тексты, темы
        $count = $this->simpleEntitiesTranslationService->translateEntities(TranslationService::DEFAULT_LANGUAGE, $language);
        ErrorLogHelper::logTranslationInfo("Всего переводов кнопок, текстов и тем сделано: $count");

        ErrorLogHelper::logTranslationInfo("Переводим масла и болезни");
        //Переводим масла и болезни
        $count = $this->mainEntitiesTranslationService->translateEntities(TranslationService::DEFAULT_LANGUAGE, $language);
        ErrorLogHelper::logTranslationInfo("Всего переводов масел и болезней сделано: $count");

        ErrorLogHelper::logTranslationInfo("Переводим фронтенд");
        //Переводим фронтенд
        $count = $this->frontendEntitiesTranslationService->translateEntities(TranslationService::DEFAULT_LANGUAGE, $language);
        ErrorLogHelper::logTranslationInfo("Всего переводов фронтенда сделано: $count");

        ErrorLogHelper::logTranslationInfo("Переводим страницы");
        //Переводим страницы
        $count = $this->pagesTranslationService->translateEntities(TranslationService::DEFAULT_LANGUAGE, $language);
        ErrorLogHelper::logTranslationInfo("Всего переводов страниц сделано: $count");

        ErrorLogHelper::logTranslationInfo("Закончили переводить");

        return true;
    }
}