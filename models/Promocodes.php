<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "promocodes".
 *
 * @property int $id
 * @property string $code
 * @property int $tariff_id
 * @property int|null $used_count
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $expire_at
 * @property string|null $payment_id
 *
 * @property Tariffs $tariff
 */
class Promocodes extends \yii\db\ActiveRecord
{
    const PROMO_CODE_CACHE_PREFIX = 'promocode-for-user-id-';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promocodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'tariff_id'], 'required'],
            [['tariff_id', 'used_count'], 'integer'],
            [['created_at', 'updated_at', 'expire_at', 'payment_id'], 'safe'],
            [['code'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'tariff_id' => 'Тариф',
            'used_count' => 'Количество использований',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'expire_at' => 'Истекает',
            'payment_id' => 'ID в платежной системе',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->expire_at || strtotime($this->expire_at) > time()) {
            return true;
        }

        return false;
    }

    public static function buildCacheKey($userId): string
    {
        return self::PROMO_CODE_CACHE_PREFIX . $userId;
    }


    public function getTariff(): ActiveQuery
    {
        return $this->hasMany(Tariffs::class, ['type' => 'tariff_id']);
    }
}
