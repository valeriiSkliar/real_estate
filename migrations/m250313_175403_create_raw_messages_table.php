<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%raw_messages}}`.
 */
class m250313_175403_create_raw_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->createTable('{{%raw_messages}}', [
            'id'          => $this->primaryKey(),
            'platform'    => $this->string()->notNull(),
            'text'        => $this->text()->notNull(),
            'author'      => $this->string()->notNull(),
            'chat_id'     => $this->string()->notNull(),
            'media_list'  => $this->json()->null(),
            'created_at'  => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'  => $this->dateTime()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%raw_messages}}');
    }
}
