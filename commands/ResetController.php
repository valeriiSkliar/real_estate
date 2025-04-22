<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;


class ResetController extends Controller
{
    public function actionIndex()
    {
        $url = "https://api.telegram.org/bot"
            . \Yii::$app->params['bot_token']
            . "/setWebHook?url="
            . \Yii::$app->params['url']
            . "/telegram/process"
            . "&drop_pending_updates=True";

        $result = file_get_contents($url);

        print $result;

        return ExitCode::OK;
    }
}