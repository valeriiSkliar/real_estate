<?php

namespace app\controllers\admin;

use app\commands\ConsoleCommandJob;
use app\components\services\translations\TranslationService;
use Yii;
use yii\web\Response;

class QueueController extends AdminController
{
    public function actionCacheClear(): Response
    {
        Yii::$app->queue->push(new ConsoleCommandJob([
            'command' => 'php yii cache/flush-all',
        ]));

        Yii::$app->session->setFlash('success', 'Кэш очищен.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionTranslateAllEntities($language): Response
    {
        Yii::$app->queue->push(new ConsoleCommandJob([
            'command' => "php yii multilanguage/generate-copies-for-new-language $language",
        ]));

        Yii::$app->session->setFlash('success', 'Переводы запущены.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCopyOriginal(): Response
    {
        Yii::$app->queue->push(new ConsoleCommandJob([
            'command' => "php yii frontend-translation/copy-original",
        ]));

        Yii::$app->session->setFlash('success', 'Копирование запущено.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCopyTranslations(): Response
    {
        Yii::$app->queue->push(new ConsoleCommandJob([
            'command' => "php yii frontend-translation/copy-translations",
        ]));

        Yii::$app->session->setFlash('success', 'Копирование запущено.');

        return $this->redirect(Yii::$app->request->referrer);
    }
    public function actionCopyToProd(): Response
    {
        Yii::$app->queue->push(new ConsoleCommandJob([
            'command' => "php yii frontend-translation/copy-from-test-to-prod",
        ]));

        Yii::$app->session->setFlash('success', 'Копирование запущено.');

        return $this->redirect(Yii::$app->request->referrer);
    }


}