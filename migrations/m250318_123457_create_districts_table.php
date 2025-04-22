<?php

use yii\db\Migration;

class m250318_123457_create_districts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%districts}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'city_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->integer()->notNull()->defaultValue(0),
        ]);

        // При необходимости можно добавить индексы или внешние ключи, например:
        // $this->createIndex('idx-districts-city_id', '{{%districts}}', 'city_id');
        // $this->addForeignKey('fk-districts-city_id', '{{%districts}}', 'city_id', '{{%cities}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Если добавлялись внешние ключи или индексы, их нужно удалить перед удалением таблицы
        // $this->dropForeignKey('fk-districts-city_id', '{{%districts}}');
        // $this->dropIndex('idx-districts-city_id', '{{%districts}}');

        $this->dropTable('{{%districts}}');
    }
}
