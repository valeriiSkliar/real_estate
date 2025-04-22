<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telegram_chats}}`.
 */
class m241225_083803_create_telegram_chats_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%telegram_chats}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->bigInteger()->notNull()->unique(),
            'name' => $this->string()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%telegram_chats}}');
    }
}
