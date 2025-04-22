<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bot_users}}`.
 */
class m230922_135132_create_bot_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bot_users}}', [
            'id'                    => $this->primaryKey(),
            'uid'                   => $this->bigInteger()->unsigned()->null(),
            'username'              => $this->string(),
            'email'                 => $this->string(255),
            'password_hash'         => $this->string(255),
            'auth_key'              => $this->string(255),
            'auth_key_expired_at'   => $this->bigInteger(),
            'fio'                   => $this->string(),
            'created_at'            => $this->dateTime(),
            'status'                => $this->integer(),
            'phone'                 => $this->string(),
            'role_id'               => $this->integer()->defaultValue(0),
            'chat_id'               => $this->string(),
            'on_call'               => $this->boolean()->defaultValue(0),
            'priority'              => $this->integer(),
            'chat_status'           => $this->boolean()->defaultValue(0),
            'notification_on'       => $this->boolean()->defaultValue(1),
            'bonus'                 => $this->integer()->defaultValue(0),
            'language'              => $this->string()->defaultValue('ru'),
            'is_paid'               => $this->boolean()->defaultValue(0),
            'tariff'                => $this->tinyInteger()->defaultValue(0),
            'paid_until'            => $this->dateTime()->defaultValue(null),
            'trial_until'           => $this->dateTime()->defaultValue(null),
            'email_verified'        => $this->string(),
            'image'                 => $this->string(),
            'referral_id'           => $this->integer(),
            'oauth_id'              => $this->string(),
            'last_visited_at'       => $this->dateTime()->defaultValue(null),
            'payment_email'         => $this->string()->defaultValue(null),
            'has_first_deposit'     => $this->boolean()->defaultValue(false),
            'total_paid'            => $this->float()->defaultValue(0),
            'total_fees'            => $this->float()->defaultValue(0),
            'city_id'               => $this->integer(),
        ]);

        $this->createIndex(
            'idx-unique-auth_key',
            '{{%bot_users}}',
            'auth_key',
            true
        );

        $this->createIndex(
            'idx-unique-phone',
            '{{%bot_users}}',
            'phone',
            true
        );
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bot_users}}');
    }
}
