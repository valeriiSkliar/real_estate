<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%referrals}}`.
 */
class m230926_162401_create_referrals_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%referrals}}', [
            'id' => $this->primaryKey(),
            'parent_id' =>  $this->bigInteger()->unsigned(),
            'referral_id' =>  $this->bigInteger()->unsigned(),
            'created_at' => $this->date(),
            'parent_username' => $this->string(),
            'referral_username' => $this->string()
        ]);

        // Add indexes
        $this->createIndex(
            'idx-referrals-parent_id',
            '{{%referrals}}',
            'parent_id'
        );

        $this->createIndex(
            'idx-referrals-referral_id',
            '{{%referrals}}',
            'referral_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop indexes
        $this->dropIndex(
            'idx-referrals-parent_id',
            '{{%referrals}}'
        );

        $this->dropIndex(
            'idx-referrals-referral_id',
            '{{%referrals}}'
        );

        $this->dropTable('{{%referrals}}');
    }

}
