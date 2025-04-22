<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "raw_messages".
 *
 * @property int $id
 * @property string $platform
 * @property string $text
 * @property string $author
 * @property string $chat_id
 * @property string|null $media_list
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class RawMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'raw_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['platform', 'text', 'author', 'chat_id'], 'required'],
            [['text'], 'string'],
            [['media_list', 'created_at', 'updated_at'], 'safe'],
            [['platform', 'author', 'chat_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform' => 'Platform',
            'text' => 'Text',
            'author' => 'Author',
            'chat_id' => 'Chat ID',
            'media_list' => 'Media List',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
