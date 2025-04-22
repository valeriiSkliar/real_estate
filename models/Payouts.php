<?php

namespace app\models;

use app\components\services\bot\TelegramService;
use app\helpers\ErrorLogHelper;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "payouts".
 *
 * @property int $id
 * @property int $uid
 * @property int|null $telegram_id
 * @property string|null $username
 * @property int|null $amount
 * @property int|null $status
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property BotUsers $u
 */
class Payouts extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT =  0;
    const STATUS_ADDED =  1;
    const STATUS_CLOSED =  2;
    const STATUS_WITHDRAWN =  10;

    public static function statusList(): array
    {
        return [
            self::STATUS_DRAFT => 'Ожидает',
            self::STATUS_ADDED => 'Начислено',
            self::STATUS_WITHDRAWN => 'Снято',
            self::STATUS_CLOSED => 'Закрыто',
        ];
    }
    public static function statusLabel($status): string
    {
        $class = match ($status) {
            self::STATUS_DRAFT => 'badge bg-danger',
            self::STATUS_ADDED => 'badge bg-success',
            self::STATUS_CLOSED => 'badge bg-secondary',
            default => 'badge bg-primary',
        };

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payouts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['uid', 'telegram_id', 'amount', 'status'], 'integer'],
            [['created_at', 'updated_at', 'username'], 'safe'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::class, 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Пользователь',
            'telegram_id' => 'Telegram ID',
            'amount' => 'Количество',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'username' => 'Пользователь',
        ];
    }

    public function beforeSave($insert)
    {
        $this->updated_at = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[U]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'uid']);
    }

    public static function sendPayoutNotification($telegramId, $amount, $status = Payouts::STATUS_ADDED): void
    {
        try {
            $slug = ($status == Payouts::STATUS_ADDED) ? 'fill-up-success' : 'withdraw-success';
            $textEntity = Texts::find()->where(['slug' => $slug])->one();
            $message =  $textEntity?->name . ' ' . $amount;

            /** @var TelegramService $telegramService */
            $telegramService = Yii::$app->telegramService;
            $telegramService->sendMessageByMarkup($message, $telegramId);
        } catch (Throwable $e) {
            ErrorLogHelper::logPayoutInfo(
                $e->getMessage() . $e->getTraceAsString(),
                'Ошибка уведомления пользователя, при выводе ' . $telegramId
            );
        }
    }

    public static function saveNewPayout($user, $amount, $status): bool
    {
        try {
            $payout = new Payouts([
                'uid' => $user->id,
                'telegram_id' => $user->uid,
                'username' => $user->username,
                'amount' => $amount,
                'status' => $status,
            ]);

            return $payout->save();
        } catch (Throwable $e) {
            ErrorLogHelper::logPayoutInfo(
                $e->getMessage() . $e->getTraceAsString(),
                'Ошибка сохранения новой записи'
            );

            return false;
        }
    }
}
