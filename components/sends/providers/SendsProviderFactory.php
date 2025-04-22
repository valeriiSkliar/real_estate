<?php

namespace app\components\sends\providers;

use app\components\sends\interfaces\SendsProviderInterface;
use app\models\Sends;

class SendsProviderFactory
{
    /**
     * Метод для создания провайдера рассылки
     *
     * @param int   $type
     * @param Sends $send
     *
     * @return SendsProviderInterface
     */
    public static function createProvider(int $type, Sends $send): SendsProviderInterface
    {
        return match ($type) {
            0 => new CommonSendsProvider($send),
            1 => new TelegramSendsProvider($send),
            2 => new WebSendsProvider($send),
            3 => new MobileSendsProvider($send),
            default => throw new \RuntimeException("Unknown provider type: $type"),
        };
    }
}