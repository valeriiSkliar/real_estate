<?php

use yii\db\Migration;

/**
 * Class m230922_182213_fill_owner_to_user_table
 */
class m230922_182213_fill_owner_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%user}}', [
            'id' => 1,
            'username' => 'owner',
            'auth_key' => 'AtWyiBDpI7_ox7k1rmriVofyKq5L6x5I',
            'email' => 'admin@example.com',
            'password_hash' => '$2y$13$.eYAW/OQPjGJP/Y5W4YSJuqMMDQR3xRWDxgfdqoP2qnk1Iqi9sfvy',
            'created_at' => date('Y-m-d', time()),
            'updated_at' => date('Y-m-d', time()),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}', ['id' => 1]);
    }
}
