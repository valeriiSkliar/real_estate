<?php
namespace app\components;

use app\models\BotUsers;
use Yii;
use yii\helpers\Url;

class RouteBuilder extends Url
{
    public static function toApi(): string
    {
        return self::to(getenv('URL'));
    }

    public static function toBot(): string
    {
        return self::to(getenv('BOT_LINK'));
    }
}