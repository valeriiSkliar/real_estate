<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%support_messages}}`.
 */
class m250407_123456_create_support_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%support_messages}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'status' => $this->integer()->defaultValue(0),
            'created_at'        => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'        => $this->dateTime()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%support_messages}}');
    }
}
