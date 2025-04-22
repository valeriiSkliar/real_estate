<?php


use yii\db\Migration;

/**
 * Handles the creation of table `{{%advertisement_sections}}`.
 */
class m250314_000001_create_advertisement_sections_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%advertisement_sections}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'sort' => $this->integer()->notNull()->defaultValue(0),
            // Используем SQL-тип ENUM для поля type с набором допустимых значений
            'type' => "ENUM('app','house','land') NOT NULL",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%advertisement_sections}}');
    }
}
