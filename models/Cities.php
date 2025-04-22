<?php

namespace app\models;

use app\enums\ActiveStatuses;
use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $order
 * @property int $active
 */
class Cities extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active'], 'default', 'value' => 1],
            [['slug', 'name'], 'required'],
            [['order', 'active'], 'integer'],
            [['slug', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'name' => 'Name',
            'order' => 'Order',
            'active' => 'Active',
        ];
    }

    public static function getActiveCities(): array
    {
        return self::find()->where(['active' => ActiveStatuses::ACTIVE->value])->orderBy('order')->all();
    }
}
