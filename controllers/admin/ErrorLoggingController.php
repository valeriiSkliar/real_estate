<?php

namespace app\controllers\admin;

use app\helpers\LogInfoHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;


class ErrorLoggingController extends AdminController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class'   => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex($name): string
    {
        return $this->render('index', [
            'name' => $name,
        ]);
    }

    public function actionFetchLogs($name): Response
    {
        $logFile = Yii::getAlias('@app/runtime/logs/') . $name . '.log';

        $response = LogInfoHelper::fetchLogs($logFile, $name) ;

        return $response ? $this->asJson($response) : $this->asJson('');
    }

    public function actionClearLog($name): Response
    {
        $logFile = Yii::getAlias('@app/runtime/logs/') . $name . '.log';

        if (LogInfoHelper::clearLog($logFile, $name)) {
            Yii::$app->session->setFlash('success', 'Лог очищен.');
        } else {
            Yii::$app->session->setFlash('error', 'Лог не найден.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
