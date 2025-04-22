<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "source_chats".
 *
 * @property int $id
 * @property string $name
 * @property string $chat_id
 * @property string $platform
 * @property int $active
 * @property string|null $stop_words
 */
class SourceChats extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'source_chats';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stop_words'], 'default', 'value' => null],
            [['active'], 'default', 'value' => 1],
            [['name', 'chat_id', 'platform'], 'required'],
            [['active'], 'integer'],
            [['name', 'chat_id', 'platform', 'stop_words'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'chat_id' => 'Chat ID',
            'platform' => 'Platform',
            'active' => 'Active',
            'stop_words' => 'Stop Words',
        ];
    }

}
