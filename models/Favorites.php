<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favorites".
 *
 * @property int $id
 * @property int $user_id
 * @property int $advertisement_id
 */
class Favorites extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'advertisement_id'], 'required'],
            [['user_id', 'advertisement_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'advertisement_id' => 'Advertisement ID',
        ];
    }
}
