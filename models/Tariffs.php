<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tariffs".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $price
 * @property int $type
 * @property string|null $description
 * @property string|null $currency
 * @property string|null $discount
 * @property string|null $link
 * @property string|null $language
 * @property string $provider
 * @property string|null $uuid
 * @property string|null $currency_code
 * @property string|null $payment_description
 * @property string|null $bank_provider
 * @property boolean $is_main
 * @property float $fee
 * @property string|null $subscription_id
 * @property int|null $subscription_period_days
 * @property int $status
 */
class Tariffs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariffs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price', 'type', 'is_main', 'fee', 'subscription_period_days', 'status'], 'integer'],
            [['description', 'language', 'provider', 'payment_description', 'bank_provider', 'subscription_id'], 'string'],
            [['name', 'currency', 'link', 'discount', 'uuid', 'currency_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'price' => 'Цена',
            'description' => 'Описание',
            'currency' => 'Валюта',
            'discount' => 'Скидка',
            'link' => 'Ссылка',
            'language' => 'Язык',
            'type' => 'Тип',
            'uuid' => 'Код продукта',
            'currency_code' => 'Код валюты',
            'provider' => 'Провайдер',
            'payment_description' => 'Описание оплаты',
            'is_main' => 'Видимость',
            'bank_provider' => 'Банк провайдер',
            'fee' => 'Комиссия',
            'subscription_period_days' => 'Периодичность оплаты подписки в днях',
            'subscription_id' => 'ID подписки',
            'status' => 'Статус',
        ];
    }
}
