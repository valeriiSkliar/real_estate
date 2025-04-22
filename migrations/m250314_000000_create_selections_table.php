<?php


use yii\db\Migration;

/**
 * Handles the creation of table `{{%selections}}`.
 */
class m250314_000000_create_selections_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%selections}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'realtor_id' => $this->integer()->notNull(),
            'advertisement_list' => $this->json(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%selections}}');
    }
}
