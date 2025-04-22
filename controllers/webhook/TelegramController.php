<?php

namespace app\controllers\webhook;

use app\components\telegram\handlers\TelegramFlowHandler;
use app\helpers\ErrorLogHelper;
use Exception;
use yii\web\Controller;

class TelegramController extends Controller
{
    public $enableCsrfValidation = false;

    public function __construct(
        $id,
        $module,
        $config = [],
        private readonly TelegramFlowHandler $flowHandler
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * @return void
     *
     * Основной метод куда стучится Телеграм
     */
    public function actionProcess(): void
    {
        try {
            $this->flowHandler->handle();

            return;
        } catch (Exception $e) {
            ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'telegram-error');
        }
    }

}