<?php

namespace app\commands;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class ConsoleCommandJob extends BaseObject implements JobInterface
{
    public $command;

    public function execute($queue)
    {
        exec($this->command);
    }
}