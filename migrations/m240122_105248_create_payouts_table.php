<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payouts}}`.
 */
class m240122_105248_create_payouts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payouts}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull(),
            'telegram_id' =>  $this->bigInteger()->unsigned(),
            'username' =>  $this->string(),
            'amount' =>  $this->integer(),
            'status' => $this->integer(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime(),
        ]);
        $this->createIndex('{{%idx-payouts-uid}}', '{{%payouts}}', 'uid');
        $this->addForeignKey('{{%fk-payouts-uid}}', '{{%payouts}}', 'uid', '{{%bot_users}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-payouts-uid}}', '{{%payouts}}');
        $this->dropIndex('{{%idx-payouts-uid}}', '{{%payouts}}');

        $this->dropTable('{{%payouts}}');
    }
}
