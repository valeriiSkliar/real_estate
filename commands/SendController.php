<?php

namespace app\commands;

use app\components\sends\providers\SendsProviderFactory;
use app\enums\SendsDestinationTypes;
use app\enums\SendsProviderTypes;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Sends;
use app\models\Texts;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\mutex\Mutex;

class SendController extends Controller
{
    /**
     * Инициализация команды с блокировкой
     *
     * @param string $id
     * @param array $params
     *
     * @return ?int
     */
    public function runAction($id, $params = []): ?int
    {
        // Имя замка может быть уникальным для каждой команды
        $mutexKey = self::class . '/' . $id;

        /** @var Mutex $mutex */
        $mutex = Yii::$app->mutex;

        if (!$mutex->acquire($mutexKey)) {
            $this->stdout("Команда уже выполняется.\n");
            return ExitCode::OK;
        }

        try {
            return parent::runAction($id, $params);
        } finally {
            $mutex->release($mutexKey);
        }
    }

    public function actionIndex(): void
    {
        $sends = Sends::getPendingSends();

        foreach ($sends as $send) {
            try {
                ErrorLogHelper::logSendInfo('Начал рассылку id: ' . $send->id);
                $provider = SendsProviderFactory::createProvider($send->provider, $send);

                if ($provider->send()) {
                    ErrorLogHelper::logSendInfo('Закончил рассылку id: ' . $send->id);
                    $send->updateSentEntity();
                }
            } catch (\Exception $e) {
                ErrorLogHelper::logSendInfo($e->getMessage(). $e->getTraceAsString(), 'Ошибка при отправке рассылки id: ' . $send->id);
            }
        }
    }


    /**
     * Отправляет сообщение если заканчивается оплата (запуск по крону)
     *
     * @return void
     */
    public function actionPaymentExpireAlert(): void
    {
        $batchSize = 100;
        $text = Texts::find()->where(['slug' => 'expire-alert', 'language' => 'ru'])->one();
        $twoHoursAgo = date('Y-m-d H:i:s', strtotime('-2 hours'));

        $query = BotUsers::find()
            ->where(['<=', 'created_at', $twoHoursAgo])
            ->andWhere(['trial_until' => null])
            ->andWhere(['paid_until' => null]);

        foreach ($query->batch($batchSize) as $users) {
            /** @var Payments $payment */
            foreach ($users as $user) {
                echo 'Отправляем уведомление для '. $user->id . PHP_EOL;

                try {
                    $language = 'ru';
                    Yii::$app->language = $language;

                    // Создаем объект рассылки
                    $send = new Sends();
                    $send->title = null;
                    $send->description = $text?->name;
                    //TODO: Добавить выбор провайдера рассылки когда будет добавлено поддержка других
                    $send->provider = SendsProviderTypes::TELEGRAM->value;
                    $send->language = $language;
                    $send->destination = SendsDestinationTypes::ALL->value;
                    $send->file_url = null;
                    $send->is_single = true;
                    $send->recipientEntity = $user;

                    // Создаем провайдера рассылки и отправляем сообщение
                    $provider = SendsProviderFactory::createProvider($send->provider, $send);
                    $provider->waitForDelay(); // Пауза для телеграма
                    if ($provider->send()) {
                        $user->updateAttributes(['trial_until' => date('Y-m-d H:i:s')]);
                    }
                } catch (\Exception $e) {
                    Yii::error('Ошибка при отправке рассылки expire-alert: ');
                    Yii::error($e->getMessage() . $e->getTraceAsString());
                    continue;
                }
            }
        }
    }
}