<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%promocodes}}`.
 */
class m241224_125734_create_promocodes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%promocodes}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->unique()->notNull(),
            'tariff_id' => $this->integer()->notNull(),
            'used_count' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'expire_at' => $this->dateTime(),
            'payment_id' => $this->string(),
        ]);

        $this->createIndex(
            'idx-promocodes-tariff_id',
            '{{%promocodes}}',
            'tariff_id'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-promocodes-tariff_id',
            '{{%promocodes}}'
        );

        $this->dropTable('{{%promocodes}}');
    }
}
