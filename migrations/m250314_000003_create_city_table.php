<?php


use yii\db\Migration;

/**
 * Handles the creation of table `{{%city}}`.
 */
class m250314_000003_create_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cities}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'active' => $this->boolean()->notNull()->defaultValue(true),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cities}}');
    }
}
