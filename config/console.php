<?php

use app\components\services\bot\ButtonService;
use app\components\services\bot\TelegramDataParseService;
use app\components\services\bot\TelegramService;
use app\components\telegram\handlers\TelegramApiHandler;
use yii\log\FileTarget;
use yii\mutex\FileMutex;
use yii\queue\file\Queue;
use yii\queue\LogBehavior;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'language' => 'ru',
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        'webRoot'    => dirname(__DIR__) . '/web',
    ],
    'components' => [
        'mutex' => [
            'class' => FileMutex::class,
        ],
        'queue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
            'path' => '@app/runtime/queue',
            'ttr' => 100*365*24*3600,
        ],
        'buttonService' => [
            'class' => ButtonService::class,
        ],
        'telegram' => [
            'class' => TelegramApiHandler::class,
        ],
        'telegramDataParseService' => [
            'class' => TelegramDataParseService::class,
        ],
        'telegramService' => [
            'class' => TelegramService::class,
        ],
        'globalParams' => [
            'class' => 'app\components\GlobalParams',
            'domain' => null,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['payment'],
                    'logFile' => '@app/runtime/logs/payment.log',
                    'exportInterval' => 1,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['payout'],
                    'logFile' => '@app/runtime/logs/payout.log',
                    'exportInterval' => 1,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['api'],
                    'logFile' => '@app/runtime/logs/api.log',
                    'exportInterval' => 1,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['bot'],
                    'logFile' => '@app/runtime/logs/bot.log',
                    'exportInterval' => 1,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['send'],
                    'logFile' => '@app/runtime/logs/send.log',
                    'exportInterval' => 1,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['translation'],
                    'logFile' => '@app/runtime/logs/translation.log',
                    'exportInterval' => 1,
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
