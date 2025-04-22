<?php

namespace app\models;

use PHPUnit\Util\Type;
use Yii;

/**
 * This is the model class for table "logging".
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property string|null $created_at
 * @property string|null $old
 * @property string|null $new
 * @property int|null    $type
 * @property string|null $details
 */
class Logging extends \yii\db\ActiveRecord
{
    public const TYPE_PAYMENT = 2;
    public const TYPE_NEW_USER = 3;
    public const TYPE_STATUS_CHANGE = 4;

    public const TYPE = [
        self::TYPE_PAYMENT => 'Оплата',
        self::TYPE_NEW_USER => 'Новый пользователь',
        self::TYPE_STATUS_CHANGE => 'Смена статуса пользователя',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logging';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'integer'],
            [['created_at'], 'safe'],
            [['old', 'new', 'details'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'user_id'    => 'Пользователь',
            'created_at' => 'Дата',
            'old'        => 'Старое значение',
            'new'        => 'Новое',
            'type'       => 'Событие',
            'details'    => 'Детали',
        ];
    }
}
