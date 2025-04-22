<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pages}}`.
 */
class m230925_070541_create_pages_table extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->createTable('{{%pages}}', [
            'id' => $this->primaryKey(),
            'command' => $this->string(),
            'h1' => $this->string(),
            'image' => $this->string(),
            'text' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),
            'language' => $this->string()->defaultValue('ru'),
            'meta_title' => $this->string(),
            'meta_description' => $this->string(),
            'meta_keywords' => $this->string(),
            'file' => $this->string()->null(),
            'video' => $this->string(),
            'audio' => $this->string(),
        ]);

        $pages = [
            [
                'command' => 'main_page',
                'h1' => 'Главная страница',
                'text' => 'Добро пожаловать на главную страницу нашего сайта.',
            ],
            [
                'command' => 'faq',
                'h1' => 'Часто задаваемые вопросы',
                'text' => 'Здесь вы найдете ответы на самые распространенные вопросы.',
            ],
            [
                'command' => 'after-payment',
                'h1' => 'После оплаты',
                'text' => 'Информация о том, что происходит после завершения оплаты.',
            ],

            [
                'command' => 'partnership',
                'h1' => 'Партнерство',
                'text' => 'Станьте нашим партнером и получите множество преимуществ.',
            ],
            [
                'command' => 'rules',
                'h1' => 'Правила',
                'text' => 'Изучите наши правила и условия использования сервиса.',
            ],
            [
                'command' => 'support',
                'h1' => 'Поддержка',
                'text' => 'Нужна помощь? Свяжитесь с нашей службой поддержки.',
            ],
            [
                'command' => 'check-subscription',
                'h1' => 'Проверить подписку',
                'text' => 'Тариф действует до : {expireDate}.',
            ],
            [
                'command' => 'pre-start',
                'h1' => 'Заполните данные',
                'text' => 'Нажмите на кнопку и заполните необходимые поля.',
            ],
            [
                'command' => 'welcome',
                'h1' => 'Добро пожаловать',
                'text' => 'Вы зарегистрировались в нашей системе.',
            ],
            [
                'command' => 'account',
                'h1' => 'Личный кабинет',
                'text' => 'Подписка до: {expireDate},Телефон: {phone}, Почта: {email}, ФИО: {name}',
            ],
            [
                'command' => 'support-message',
                'h1' => 'Спасибо за обращение',
                'text' => 'Ваше сообщение доставлено',
            ],
        ];

        foreach ($pages as $page) {
            $this->insert('{{%pages}}', $page);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pages}}');
    }
}
