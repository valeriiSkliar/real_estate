<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_chat}}`.
 */
class m231001_091837_create_user_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_chat}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'operator_id' => $this->integer(),
            'message' => $this->string(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_chat}}');
    }
}
