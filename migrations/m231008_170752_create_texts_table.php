<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%texts}}`.
 */
class m231008_170752_create_texts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%texts}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->notNull(),
            'name' => $this->text()->notNull(),
            'language' => $this->string()->defaultValue('ru')
        ]);

        $this->insert('{{%texts}}', [
            'slug' => 'invite-user-does-not-exist',
            'name' => 'Пользователь указанный в инвайт ссылке не существует',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'please-complete-registration',
            'name' => 'Пожалуйста нажмите на кнопку ниже для завершения регистрации.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'welcome-text',
            'name' => 'Добро пожаловать в Visa.pro!',

        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'need-to-buy-premium',
            'name' => 'Для оплаты выберите тариф',

        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-services',
            'name' => 'Выберите сервис:',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-partner-admin',
            'name' => 'Выберите действие(админ)',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-partner-user',
            'name' => 'Выберите действие:',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-name-or-id',
            'name' => 'Введите имя(username) или id (1234567890) для поиска:',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'not-yet-referrals',
            'name' => 'Пока еще нет рефералов.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'operators-are-busy',
            'name' => 'Все операторы заняты, но мы записали ваше обращение.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'call-to-operator',
            'name' => 'У вас обращение от пользователя: {fio} - {username}. Вы переключены в режим диалога. Все ваши сообщения направляются пользователю.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'call-to-user',
            'name' => 'Ваше обращение направлено оператору. Вы переключены в режим диалога. Все ваши сообщения направляются оператору.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'finish-dialog',
            'name' => 'Диалог завершен.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-all-services',
            'name' => 'Выберите сервис:',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'invite-link',
            'name' => 'Ссылка для приглашения участников: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'error-message',
            'name' => 'Произошла ошибка. Пожалуйста свяжитесь со службой поддержки.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'enter-promo-code',
            'name' => 'Введите промокод:',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'not-found-promo-code',
            'name' => 'Промокод не найден или недействителен.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'withdraw-process',
            'name' => 'Заявка отправлена и ожидает обработки (вывод может занять до 3 суток)',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'withdraw-request',
            'name' => 'Новая заявка на вывод средств',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'user-not-found',
            'name' => 'Пользователь не найден.',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'expire-alert',
            'name' => 'Вы не оплатили заказ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'tariff-link',
            'name' => 'Ссылка на тариф',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'click-to-buy',
            'name' => 'Нажмите на ссылку для оплаты тарифа {name}',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'search-not-found',
            'name' => 'Ничего не найдено по вашему запросу',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'email-request',
            'name' => 'Введите электронную почту',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-payment-method',
            'name' => 'Выберите способ оплаты',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'search-result',
            'name' => 'Результаты поиска: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'advertisement',
            'name' => 'Объявления: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'selections',
            'name' => 'Подборки: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'favorites',
            'name' => 'Избранное: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'choose-city',
            'name' => 'Выберите город: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'enter-name',
            'name' => 'Введите ФИО(в строку) и нажмите отправить: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'enter-phone',
            'name' => 'Введите телефон(+7...) и нажмите отправить: ',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'phone_unique_error',
            'name' => 'Пользователь с указанным телефоном уже существует',
        ]);
        $this->insert('{{%texts}}', [
            'slug' => 'email_unique_error',
            'name' => 'Пользователь с указанным email уже существует',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%texts}}');
    }
}
