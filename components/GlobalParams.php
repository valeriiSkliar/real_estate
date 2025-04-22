<?php

namespace app\components;

use yii\base\Component;

class GlobalParams extends Component
{
    public ?string $domain = null;
    public ?string $environment = 'web';
}