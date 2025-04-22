<?php

use app\assets\NpmAsset;
use app\components\services\bot\ButtonService;
use app\components\services\bot\TelegramDataParseService;
use app\components\services\bot\TelegramService;
use app\components\telegram\handlers\TelegramApiHandler;
use yii\log\FileTarget;
use yii\queue\file\Queue;
use yii\queue\LogBehavior;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'language' => 'ru',
    'bootstrap' => ['log', 'queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        'webRoot'    => dirname(__DIR__) . '/web',
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                // ...
                'app\assets\NpmAsset' => [
                    'js' => [
                        'jquery/dist/jquery.min.js',
                        'bootstrap/dist/js/bootstrap.bundle.min.js',
                    ],
                    'css' => [
                        'bootstrap/dist/css/bootstrap.min.css',
                    ],
                ],
            ],
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'linkAssets' => true,
            'converter' => [
                'class' => 'yii\web\AssetConverter',
                'commands' => [
                    'js' => ['js', 'copy {from} {to}'],
                    'css' => ['css', 'copy {from} {to}'],
                ],
            ],
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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '3A_OPPrxleXFF2Oz6LZHpmpOkJjtMpjO',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'queue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
            'path' => '@app/runtime/queue',
            'ttr' => 100 * 365 * 24 * 3600,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['admin/site/login'],
            'identityCookie' => ['name' => '_adminIdentity', 'httpOnly' => true],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
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

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ],
    ],
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'only' => ['admin/*'],
        'rules' => [
            [
                'actions' => ['login'], // разрешаем доступ к экшену login для гостей
                'allow' => true,
                'roles' => ['?'], // символ ? означает гостей
            ],
            [
                'allow' => true,
                'roles' => ['@'], // все остальные действия доступны только авторизованным
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
