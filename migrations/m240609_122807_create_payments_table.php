<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payments}}`.
 */
class m240609_122807_create_payments_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%payments}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => $this->integer()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'uuid' => $this->string()->unique(),
            'promo_code' => $this->string()->defaultValue(null),
            'acknowledged' => $this->boolean()->defaultValue(false),
            'notified' => $this->boolean()->defaultValue(false)
        ]);

        // Add foreign key for table `user`
        $this->addForeignKey(
            'fk-payments-user_id',
            '{{%payments}}',
            'user_id',
            '{{%bot_users}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key for table `user`
        $this->dropForeignKey(
            'fk-payments-user_id',
            '{{%payments}}'
        );

        $this->dropTable('{{%payments}}');
    }
}
