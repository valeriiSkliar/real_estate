<?php

/**
 * Представление для кнопки избранного
 * 
 * @var int $propertyId ID объявления
 * @var bool $isFavorite Находится ли объявление в избранном
 * @var string $buttonClass Класс для кнопки
 * @var string $title Заголовок для кнопки
 * @var array $options Дополнительные HTML-атрибуты
 */

use yii\helpers\Html;

// Настраиваем CSS-классы для кнопки
$finalButtonClass = $buttonClass;
if ($isFavorite) {
    $finalButtonClass .= ' active';
}

// Настраиваем атрибуты кнопки
$finalOptions = $options;
$finalOptions['class'] = $finalButtonClass;
$finalOptions['data-property-id'] = $propertyId;
$finalOptions['title'] = $title;

// Подготавливаем иконку сердца в зависимости от состояния
$iconHtml = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="'
    . ($isFavorite ? 'currentColor' : 'none')
    . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide-heart-icon lucide-heart">
        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
    </svg>';

// Рендерим кнопку
echo Html::button($iconHtml, $finalOptions);
