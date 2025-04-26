<?php

namespace app\models\mock;

/**
 * MockCollections предоставляет моковые данные для подборок.
 */
class MockCollections
{
    private static $collections = [
        [
            'id' => 1,
            'name' => 'Квартиры для Иванова И.И.',
            'clientName' => 'Иванов Иван Иванович',
            'keyParams' => '2-комн, до 15 млн, ЗАО',
            'objectCount' => 5,
            'createdAt' => '2024-07-20 10:00:00',
            'updatedAt' => '2024-07-21 15:30:00',
        ],
        [
            'id' => 2,
            'name' => 'Студии для Петровой А.С.',
            'clientName' => 'Петрова Анна Сергеевна',
            'keyParams' => 'Студия, Центр, рядом с метро',
            'objectCount' => 12,
            'createdAt' => '2024-07-19 11:00:00',
            'updatedAt' => '2024-07-22 09:15:00',
        ],
        [
            'id' => 3,
            'name' => 'Дома для Сидорова В.П.',
            'clientName' => 'Сидоров Василий Петрович',
            'keyParams' => 'Дом, > 200м², Новая Рига',
            'objectCount' => 3,
            'createdAt' => '2024-07-18 14:20:00',
            'updatedAt' => '2024-07-18 14:20:00',
        ],
        [
            'id' => 4,
            'name' => 'Коммерческая недв. для ООО "Рога и Копыта"',
            'clientName' => 'ООО "Рога и Копыта"',
            'keyParams' => 'Офис, > 50м², ЦАО',
            'objectCount' => 8,
            'createdAt' => '2024-07-22 16:00:00',
            'updatedAt' => '2024-07-22 17:00:00',
        ],
        // Можно добавить больше подборок для пагинации
        [
            'id' => 5,
            'name' => 'Однушки для Кузнецовой М.Д.',
            'clientName' => 'Кузнецова Мария Дмитриевна',
            'keyParams' => '1-комн, ЮВАО, новостройка',
            'objectCount' => 15,
            'createdAt' => '2024-07-23 09:00:00',
            'updatedAt' => '2024-07-23 11:30:00',
        ],
        [
            'id' => 6,
            'name' => 'Квартиры с ремонтом',
            'clientName' => 'Смирнов Олег Викторович',
            'keyParams' => '3-комн, с ремонтом, СВАО',
            'objectCount' => 7,
            'createdAt' => '2024-07-23 10:10:00',
            'updatedAt' => '2024-07-23 12:45:00',
        ],
        [
            'id' => 7,
            'name' => 'Апартаменты в Москва-Сити',
            'clientName' => 'Волков Д. А.',
            'keyParams' => 'Апартаменты, >100м², Пресненский',
            'objectCount' => 4,
            'createdAt' => '2024-07-23 14:00:00',
            'updatedAt' => '2024-07-23 14:00:00',
        ],
        [
            'id' => 8,
            'name' => 'Инвестиционные объекты',
            'clientName' => 'Захаров П. Р.',
            'keyParams' => 'Любой тип, высокая доходность',
            'objectCount' => 22,
            'createdAt' => '2024-07-24 08:30:00',
            'updatedAt' => '2024-07-24 10:05:00',
        ],
        [
            'id' => 9,
            'name' => 'Квартиры у парка',
            'clientName' => 'Новикова Е. Г.',
            'keyParams' => '2-комн, рядом с парком, ЮЗАО',
            'objectCount' => 9,
            'createdAt' => '2024-07-24 11:00:00',
            'updatedAt' => '2024-07-24 13:20:00',
        ],
        [
            'id' => 10,
            'name' => 'Небольшие офисы в аренду',
            'clientName' => 'ИП Марков',
            'keyParams' => 'Офис, < 30м², Аренда',
            'objectCount' => 11,
            'createdAt' => '2024-07-24 15:00:00',
            'updatedAt' => '2024-07-24 15:00:00',
        ],
        [
            'id' => 11,
            'name' => 'Пентхаусы для VIP',
            'clientName' => 'Анонимный клиент',
            'keyParams' => 'Пентхаус, >200м², Хамовники',
            'objectCount' => 2,
            'createdAt' => '2024-07-25 10:00:00',
            'updatedAt' => '2024-07-25 11:00:00',
        ],
    ];

    /**
     * Возвращает все моковые подборки.
     * В реальном приложении здесь будет логика получения данных из БД.
     * @return array
     */
    public static function findAll(): array
    {
        // Сортируем по умолчанию по дате обновления (сначала новые)
        usort(self::$collections, function ($a, $b) {
            return strtotime($b['updatedAt']) - strtotime($a['updatedAt']);
        });
        return self::$collections;
    }

    /**
     * Возвращает количество моковых подборок.
     * @return int
     */
    public static function getCount(): int
    {
        return count(self::$collections);
    }
}
