<?php

namespace app\helpers;

use Yii;

class ErrorLogHelper
{
    public static function logPaymentInfo($message, $title = null): void
    {
        self::logInfo('payment', $message, $title);
    }

    public static function logPayoutInfo($message, $title = null): void
    {
        self::logInfo('payout', $message, $title);
    }

    public static function logApiInfo($message, $title = null): void
    {
        self::logInfo('api', $message, $title);
    }

    public static function logBotInfo($message, $title = null): void
    {
        self::logInfo('bot', $message, $title);
    }

    public static function logSendInfo($message, $title = null): void
    {
        self::logInfo('send', $message, $title);
    }

    public static function logTranslationInfo($message, $title = null): void
    {
        self::logInfo('translation', $message, $title);
    }

    private static function logInfo($category, $message, $title = null): void
    {
        if ($title) {
            Yii::info($title, $category);
        }

        Yii::info($message, $category);
    }

    private static function logError($category, $message, $title = null): void
    {
        if ($title) {
            Yii::info($title, $category);
        }

        Yii::info($message, $category);
    }
}