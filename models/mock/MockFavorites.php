<?php

namespace app\models\mock;

use Yii;
use yii\base\Model;

/**
 * Класс для работы с избранными объявлениями (моковая версия)
 * В реальном проекте здесь была бы модель с интеграцией в БД
 */
class MockFavorites extends Model
{
    // Статический массив для хранения моковых данных избранного
    private static $favorites = [
        [
            'id' => 1,
            'price' => '5 200 000 ₽',
            'pricePerSquareMeter' => '86 667 ₽/м²',
            'title' => '3-комн. квартира, 60 м²',
            'address' => 'ул. Красная, 176, р-н Центральный',
            'detailUrl' => '/property/1',
            'image' => '/images/property1.jpg'
        ],
        [
            'id' => 2,
            'price' => '12 500 000 ₽',
            'pricePerSquareMeter' => '104 167 ₽/м²',
            'title' => '4-комн. квартира, 120 м²',
            'address' => 'ул. Кубанская, 15, р-н Фестивальный',
            'detailUrl' => '/property/2',
            'image' => '/images/property2.jpg'
        ],
        [
            'id' => 3,
            'price' => '3 800 000 ₽',
            'pricePerSquareMeter' => '95 000 ₽/м²',
            'title' => '1-комн. квартира, 40 м²',
            'address' => 'ул. Московская, 59, р-н Центральный',
            'detailUrl' => '/property/3',
            'image' => '/images/property3.jpg'
        ],
        [
            'id' => 4,
            'price' => '7 300 000 ₽',
            'pricePerSquareMeter' => '91 250 ₽/м²',
            'title' => '2-комн. квартира, 80 м²',
            'address' => 'ул. Ставропольская, 107, р-н Черемушки',
            'detailUrl' => '/property/4',
            'image' => '/images/property4.jpg'
        ],
        [
            'id' => 5,
            'price' => '8 900 000 ₽',
            'pricePerSquareMeter' => '89 000 ₽/м²',
            'title' => '3-комн. квартира, 100 м²',
            'address' => 'ул. Северная, 326, ЖК Большой',
            'detailUrl' => '/property/5',
            'image' => '/images/property5.jpg'
        ],
    ];

    /**
     * Возвращает все избранные объявления пользователя
     * 
     * @param int $userId ID пользователя
     * @param int $limit Лимит записей
     * @param int $offset Смещение для пагинации
     * @return array Массив избранных объявлений
     */
    public static function findAll($userId = 1, $limit = 9, $offset = 0)
    {
        // В реальной системе здесь был бы запрос к БД
        return array_slice(self::$favorites, $offset, $limit);
    }

    /**
     * Возвращает количество избранных объявлений пользователя
     * 
     * @param int $userId ID пользователя
     * @return int Количество избранных объявлений
     */
    public static function getCount($userId = 1)
    {
        // В реальной системе здесь был бы запрос COUNT к БД
        return count(self::$favorites);
    }

    /**
     * Проверяет, есть ли объявление в избранном у пользователя
     * 
     * @param int $advertisementId ID объявления
     * @param int $userId ID пользователя
     * @return bool True, если объявление в избранном
     */
    public static function isInFavorites($advertisementId, $userId = 1)
    {
        // В реальной системе здесь был бы запрос к БД
        foreach (self::$favorites as $favorite) {
            if ($favorite['id'] == $advertisementId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Добавляет объявление в избранное
     * 
     * @param int $advertisementId ID объявления
     * @param int $userId ID пользователя
     * @return bool Результат операции
     */
    public static function add($advertisementId, $userId = 1)
    {
        // Проверяем, уже ли объявление в избранном
        if (self::isInFavorites($advertisementId, $userId)) {
            return true; // Уже в избранном
        }

        // В реальной системе здесь было бы добавление в БД
        // Для демонстрации добавляем в статический массив
        self::$favorites[] = [
            'id' => $advertisementId,
            'price' => rand(2, 15) . ' ' . rand(100, 999) . ' 000 ₽',
            'pricePerSquareMeter' => rand(70, 150) . ' 000 ₽/м²',
            'title' => rand(1, 4) . '-комн. квартира, ' . rand(30, 150) . ' м²',
            'address' => 'ул. Примерная, ' . rand(1, 200) . ', р-н Тестовый',
            'detailUrl' => '/property/' . $advertisementId,
            'image' => '/images/property' . rand(1, 5) . '.jpg'
        ];

        return true;
    }

    /**
     * Удаляет объявление из избранного
     * 
     * @param int $advertisementId ID объявления
     * @param int $userId ID пользователя
     * @return bool Результат операции
     */
    public static function remove($advertisementId, $userId = 1)
    {
        // В реальной системе здесь было бы удаление из БД
        // Для демонстрации удаляем из статического массива
        foreach (self::$favorites as $key => $favorite) {
            if ($favorite['id'] == $advertisementId) {
                unset(self::$favorites[$key]);
                self::$favorites = array_values(self::$favorites); // Переиндексируем массив
                return true;
            }
        }

        return false; // Объявление не найдено в избранном
    }
}
