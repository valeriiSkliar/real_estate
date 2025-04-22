<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sends}}`.
 */
class m231007_133332_create_sends_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sends}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'status' => $this->integer()->defaultValue(0),
            'file_url' => $this->string(),
            'is_regular' => $this->boolean(),
            'provider' => $this->integer(),
            'destination' => $this->string(),
            'date' => $this->datetime(),
            'language' => $this->string()->defaultValue('ru'),
            'is_single' => $this->boolean()->defaultValue(false),
            'recipient' => $this->string()->defaultValue(null),
            'video_url'=> $this->string(),
            'audio_url' => $this->string(),
            'image_url' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%sends}}');
    }
}