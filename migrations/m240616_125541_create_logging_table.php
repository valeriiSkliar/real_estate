<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logging}}`.
 */
class m240616_125541_create_logging_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logging}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'created_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'old' => $this->text(),
            'new' => $this->text(),
            'type' => $this->integer(),
            'details' => $this->text(),
        ]);

        $this->createIndex('idx-logging-user_id', '{{%logging}}', 'user_id');
        $this->createIndex('idx-logging-created_at', 'logging', 'created_at');
        $this->createIndex('idx-logging-type', 'logging', 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-logging-user_id', '{{%logging}}');
        $this->dropTable('{{%logging}}');
    }
}
