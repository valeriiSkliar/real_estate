<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use app\models\mock\MockFavorites;

$this->title = 'Избранное | goanytime.ru';

// Получаем данные из модели
$favorites = MockFavorites::findAll();
$totalCount = MockFavorites::getCount();

// Создаем датапровайдер для пагинации
$dataProvider = new ArrayDataProvider([
    'allModels' => $favorites,
    'pagination' => [
        'pageSize' => 9,
    ],
    'sort' => [
        'attributes' => ['id', 'price', 'title'],
    ],
]);

// Получаем данные для текущей страницы
$mockedFavorites = $dataProvider->getModels();

// Регистрируем CSS и JS
$this->registerCssFile('@web/css/favorites.css');
$this->registerJsFile(
    '@web/js/components/favorite-button.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

// Получаем объект пагинации из датапровайдера
$pages = $dataProvider->getPagination();

// Регистрируем JS
$this->registerJsFile(
    Yii::$app->request->baseUrl . '/js/favorites.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>

<?= app\widgets\FavoritesListWidget::widget([
    'dataProviderOptions' => [
        'allModels' => $mockedFavorites,
        'pagination' => [
            'pageSize' => 9,
        ],
    ],
    'title' => 'Избранное',
    'showTitle' => true,
    'showPagination' => true,
    'viewType' => 'grid',
    'emptyText' => 'У вас пока нет избранных объявлений',
    'options' => ['class' => 'favorites-container'],
]) ?>