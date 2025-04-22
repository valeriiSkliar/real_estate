<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_info}}`.
 */
class m240526_172326_create_user_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_info}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->text(),
            'photo_url' => $this->string(),
            'language' => $this->string(),
            'name' => $this->string(),
            'phone' => $this->string(),
            'email' => $this->string(),
            'telegram' => $this->string(),
            'whatsapp' => $this->string(),
        ]);

        $this->addForeignKey(
            'fk-user_info-user_id',
            '{{%user_info}}',
            'user_id',
            '{{%bot_users}}',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-user_info-user_id',
            '{{%user_info}}',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user_info-user_id',
            '{{%user_info}}'
        );
        $this->dropIndex(
            'idx-user_info-user_id',
            '{{%user_info}}'
        );
        $this->dropTable('{{%user_info}}');
    }
}
