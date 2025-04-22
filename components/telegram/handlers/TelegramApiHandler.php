<?php

namespace app\components\telegram\handlers;

use Telegram\Bot\Api;
use Yii;

class TelegramApiHandler extends Api
{
    public function __construct(){
        parent::__construct(Yii::$app->params['bot_token']);
    }
}