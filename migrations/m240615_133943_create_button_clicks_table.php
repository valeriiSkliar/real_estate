<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%button_clicks}}`.
 */
class m240615_133943_create_button_clicks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%button_clicks}}', [
            'id'      => $this->primaryKey(),
            'name'    => $this->string()->notNull(),
            'counter' => $this->integer()->notNull()->defaultValue(0),
            'date'    => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-button_clicks-date',
            'button_clicks',
            'date'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%button_clicks}}');
    }
}
