<?php

namespace app\components\sends\providers;

use app\components\sends\interfaces\SendsProviderInterface;

class WebSendsProvider extends AbstractSendsProvider implements SendsProviderInterface
{

    /**
     * @return array
     */
    public function getUsers(): array
    {
        // TODO: Implement getUsers() method.
        return [];
    }

    /**
     * @return mixed
     */
    public function send(): bool
    {
        return true;
    }
}