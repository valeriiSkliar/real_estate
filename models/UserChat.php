<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_chat".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $operator_id
 * @property string|null $message
 * @property string|null $created_at
 */
class UserChat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'operator_id'], 'integer'],
            [['created_at'], 'safe'],
            [['message'], 'string', 'max' => 255],
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
            'operator_id' => 'Operator ID',
            'message' => 'Message',
            'created_at' => 'Created At',
        ];
    }
}
