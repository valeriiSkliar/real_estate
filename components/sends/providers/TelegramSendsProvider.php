<?php

namespace app\components\sends\providers;

use app\components\sends\interfaces\SendsProviderInterface;
use app\components\services\bot\TelegramService;
use app\enums\SendsDestinationTypes;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use Throwable;
use Yii;
use yii\db\ActiveQuery;

class TelegramSendsProvider extends AbstractSendsProvider implements SendsProviderInterface
{
    /**
     * Делаем выборку пользователей для рассылки.
     * Возвращает объект ActiveQuery для обработки пачками
     *
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        $query = $this->initializeQuery();
        $destinations = $this->prepareDestination();

        if ($destinations) {
            $orWhereConditions = $this->buildOrWhereConditions($destinations);

            if (count($orWhereConditions) > 1) {
                $query->andWhere($orWhereConditions);
            }
        }

        return $query;
    }

    private function initializeQuery(): ActiveQuery
    {
        return BotUsers::find()
//            ->where(['language' => $this->send->language])
            ->andWhere(['not', ['uid' => '']])
            ->andWhere(['not', ['uid' => null]]);
    }

    private function buildOrWhereConditions(array $destinations): array
    {
        $orWhereConditions = ['or'];

        foreach ($destinations as $destination) {
            $destination = (int) $destination;
            $orWhereConditions[] = $this->getConditionForDestination($destination);
        }

        return $orWhereConditions;
    }

    private function getConditionForDestination(int $destination): array
    {
        return match ($destination) {
            SendsDestinationTypes::ACTIVE->value => ['>=', 'paid_until', date('Y-m-d H:i:s')],
            SendsDestinationTypes::FINISHED->value => [
                'and',
                ['<', 'paid_until', date('Y-m-d H:i:s')],
                ['is_paid' => BotUsers::PAID],
            ],
//            SendsDestinationTypes::REFERRAL->value => ['not', ['referral_id' => null]],
//            SendsDestinationTypes::NOT_REFERRAL->value => ['referral_id' => null],
            default => ['tariff' => $destination],
        };
    }

    /**
     * Отправляет рассылку пользователям пакетами по 100 штук
     *
     * @return bool
     */
    public function send(): bool
    {
        try {
            if ($this->send->is_single) {
                $user = $this->send->getRecipientEntity();

                if (!$user) {
                    ErrorLogHelper::logSendInfo('Получил неправильный recipient: '. $this->send->id);

                    return false;
                }

                if ($this->sendMessage($user)) {
                    ErrorLogHelper::logSendInfo('Отправил единичную рассылку id: ' . $this->send->id . ' пользователю ' . $user->id);
                }

                return true;
            }

            $query = $this->getUsers();
            $batchSize = 100;
            $counter = 0;

            // Получаем общее количество пользователей
            $totalUsers = $query->count();
            ErrorLogHelper::logSendInfo('Начал рассылку id: ' . $this->send->id . ' на ' . $totalUsers . ' пользователей');

            // Обрабатываем пользователей пакетами по 100
            foreach ($query->batch($batchSize) as $usersBatch) {
                foreach ($usersBatch as $user) {
                    if ($this->sendMessage($user)) {
                        ++$counter;
                        ErrorLogHelper::logSendInfo('Отправил рассылку id: ' . $this->send->id . ' пользователю ' . $user->uid);
                    }
                }

                // Очистка памяти после обработки каждого пакета
                unset($usersBatch);
                gc_collect_cycles(); // Вызов сборщика мусора для освобождения памяти
            }

            ErrorLogHelper::logSendInfo('Завершил рассылку id: ' . $this->send->id . ' и отправлено для ' . $counter . ' пользователей');

            return true;
        } catch (Throwable $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString());

            return false;
        }
    }

    /**
     * Отправляет сообщение пользователю
     *
     * @param $user
     * @return bool
     */
    public function sendMessage($user): bool
    {
        try {
            Yii::$app->language = $user->language;
            /** @var TelegramService $telegramService */
            $telegramService = Yii::$app->telegramService;
            $telegramService->setTelegramUserId($user->uid);

            $telegramService->sendMedia($this->send);

            $text = $this->send->title
                ? "<b>" . $this->send->title . "</b>" . PHP_EOL . $this->send->description
                : $this->send->description;

            $telegramService->sendPhoto($this->send->file_url);
            $telegramService->sendMenu($text);

            return true;
        } catch (Throwable $e) {
            ErrorLogHelper::logSendInfo($e->getMessage() . $e->getTraceAsString(), 'Ошибка при отправке рассылки пользователю: ' . $user->uid);

            return false;
        }
    }
}
