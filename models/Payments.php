<?php

namespace app\models;

use app\enums\PaymentStatuses;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $amount
 * @property int $user_id
 * @property string $created_at
 * @property int $status
 * @property int $tariff_id
 * @property string $uuid
 * @property string $promo_code
 * @property bool $acknowledged
 * @property bool $notified
 *
 * @property BotUsers $user
 */
class Payments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'user_id', 'status', 'tariff_id'], 'required'],
            [['amount', 'user_id', 'status', 'tariff_id'], 'integer'],
            [['created_at', 'uuid', 'promo_code', 'acknowledged', 'notified'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Amount',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'status' => 'Status',
            'tariff_id' => 'Tariff ID',
            'uuid' => 'Uuid',
            'promo_code' => 'Promo Code',
            'acknowledged' => 'Acknowledged',
            'notified' => 'Notified',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(BotUsers::class, ['id' => 'user_id']);
    }

    public function getTariff(): ActiveQuery
    {
        return $this->hasOne(Tariffs::class, ['id' => 'tariff_id']);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        try {
            $log = new Logging();
            $log->user_id = $this->user_id;
            $log->type = Logging::TYPE_PAYMENT;
            $log->details = $this->amount;

            if ($insert) {
                $log->old = '';
                $log->new = 'Заказ создан';
            } else if (isset($changedAttributes['status'])) {
                $oldStatusValue = $changedAttributes['status'];
                $log->old = PaymentStatuses::getPaymentName($oldStatusValue);
                $log->new = PaymentStatuses::getPaymentName($this->status);
            }

            $log->save(false);
        } catch (Exception $e) {
            Yii::error('error occurs when saving new TopUps in log');
        }

    }

    public function incrementPromoCodeCount(): void
    {
        try {
            if (!$this->promo_code) {
                return;
            }

            $promoCode = Promocodes::findOne(['code' => $this->promo_code]);

            if (!$promoCode) {
                return;
            }

            $promoCode->used_count++;
            $promoCode->save();

        } catch (Throwable $e) {
            return;
        }
    }
}
