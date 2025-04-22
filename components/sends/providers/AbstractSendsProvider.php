<?php

namespace app\components\sends\providers;

use app\models\Sends;

abstract class AbstractSendsProvider
{
    public Sends $send;

    public function __construct(Sends $send){
        $this->send = $send;
    }

    /**
     * Метод для получения групп пользователей, которым будет отправлена рассылка
     *
     * @return array
     */
    public function prepareDestination(): array
    {
        $destinations = explode(',', $this->send->destination);

        if (in_array('0', $destinations)) {
            return [];
        }

        return $destinations;
    }

    /**
     * Необходимо выдерживать паузу при рассылке в телеграм, иначе АПИ телеграма блокирует входящие сообщения
     * @return void
     */
    public function waitForDelay(): void
    {
        sleep(1);
    }
}