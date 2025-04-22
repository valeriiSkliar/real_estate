<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%buttons}}`.
 */
class m230923_073922_create_buttons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%buttons}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'name' => $this->string(),
            'type' => $this->integer()->defaultValue(0),
            'position' => $this->integer()->defaultValue(0),
            'priority' => $this->integer(),
            'link' => $this->string(),
            'web_app_link' => $this->string(),
            'is_hidden' => $this->boolean()->defaultValue(0),
            'language'=> $this->string()->defaultValue('ru')
        ]);

        $this->insert('{{%buttons}}', [
            'slug' => 'tariffs',
            'name' => 'Выбрать тариф',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'faq',
            'name' => 'FAQ',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'support',
            'name' => 'Техподдержка',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'check-subscription',
            'name' => 'Проверить подписку',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'partnership',
            'name' => 'Партнерская программа',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'my-referrals',
            'name' => 'Мои рефералы',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'account',
            'name' => 'Мой рефферальный кабинет',
            'position' => '3',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'all-referrals',
            'name' => 'Все рефералы',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'start',
            'name' => 'Назад',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'rules',
            'name' => 'Правила',
            'position' => '9',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'accept-rules',
            'name' => 'Принимаю',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'back-to-rules',
            'name' => 'Назад',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'share',
            'name' => 'Поделиться ссылкой',
            'position' => '3',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'search',
            'name' => 'Поиск',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'advertisements',
            'name' => 'Объявления',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'selections',
            'name' => 'Подборки',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'favorites',
            'name' => 'Избранное',
            'position' => '1',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'choose-city',
            'name' => 'Выбрать город',
            'type' => '1',
        ]);
        $this->insert('{{%buttons}}', [
            'slug' => 'tariff-id-1',
            'name' => 'Тариф Премиум в руб',
            'type' => '1',
            'position' => '8',
            'is_hidden' => '0',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%buttons}}');
    }
}
