<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tariffs}}`.
 */
class m240609_094655_create_tariffs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tariffs}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'price' => $this->integer(),
            'description' => $this->text(),
            'currency' => $this->string(),
            'discount' => $this->string()->defaultValue(0),
            'link' => $this->string(),
            'language' => $this->string(),
            'type' => $this->integer(),
            'provider' => $this->string(),
            'uuid' => $this->string()->defaultValue(null),
            'currency_code' => $this->string()->defaultValue(null),
            'is_main' => $this->boolean()->defaultValue(false),
            'payment_description' => $this->text(),
            'bank_provider' => $this->string(),
            'fee' => $this->float()->defaultValue(0),
            'subscription_id' => $this->string()->defaultValue(null),
            'subscription_period_days' => $this->integer()->defaultValue(null),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tariffs}}');
    }
}
