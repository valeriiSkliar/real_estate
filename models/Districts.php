<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "districts".
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $city_id
 * @property int $order
 * @property int $status
 */
class Districts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'districts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 0],
            [['slug', 'name', 'city_id'], 'required'],
            [['city_id', 'order', 'status'], 'integer'],
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
            'city_id' => 'City ID',
            'order' => 'Order',
            'status' => 'Status',
        ];
    }

}
