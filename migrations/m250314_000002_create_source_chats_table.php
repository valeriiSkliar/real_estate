<?php


use yii\db\Migration;

/**
 * Handles the creation of table `{{%source_chats}}`.
 */
class m250314_000002_create_source_chats_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%source_chats}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'chat_id' => $this->string()->notNull(),
            'platform' => $this->string()->notNull(),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'stop_words' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%source_chats}}');
    }
}
