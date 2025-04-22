<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "support_messages".
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property int|null $status
 * @property string $created_at
 * @property string $updated_at
 */
class SupportMessages extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'support_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 0],
            [['user_id', 'text'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'text' => 'Текст',
            'status' => 'Статус',
            'created_at' => 'Создано в',
            'updated_at' => 'Updated At',
        ];
    }

    public static function saveMessage(string $text, int $userId)
    {
        $model = new SupportMessages();
        $model->user_id = $userId;
        $model->text = $text;
        $model->save();
    }
}
