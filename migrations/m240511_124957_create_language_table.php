<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%languages}}`.
 */
class m240511_124957_create_language_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%languages}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%languages}}');
    }
}
