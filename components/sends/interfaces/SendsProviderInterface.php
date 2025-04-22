<?php

namespace app\components\sends\interfaces;

use yii\db\ActiveQuery;

interface SendsProviderInterface
{
    /**
     * Метод для получения пользователей, которым будет отправлена рассылка
     *
     * @return array|ActiveQuery
     */
    public function getUsers(): array|ActiveQuery;

    /**
     * Метод для отправки рассылки
     * @return bool
     */
    public function send(): bool;
}