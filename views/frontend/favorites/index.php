<?php

/** @var yii\web\View $this */
/** @var string $activeTab Идентификатор активной вкладки ('favorites' или 'collections') */

use yii\bootstrap5\Html;
// Убираем ненужные use
// use yii\bootstrap5\LinkPager;
// use yii\data\Pagination;
// use yii\data\ArrayDataProvider;
// use yii\helpers\Url;
// use app\models\mock\MockFavorites;
use app\widgets\FavoritesListWidget;
use app\widgets\CollectionsListWidget;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Url; // Добавляем для Url::to()

// Регистрируем JS-плагины Bootstrap (нужны для работы табов)
BootstrapPluginAsset::register($this);

// Определяем заголовки в зависимости от вкладки
$this->title = ($activeTab === 'collections' ? 'Подборки' : 'Избранное') . ' | goanytime.ru';

// Убираем получение данных и датапровайдер из представления,
// так как виджет FavoritesListWidget делает это сам.
// $favorites = MockFavorites::findAll();
// $totalCount = MockFavorites::getCount();
// $dataProvider = new ArrayDataProvider([...]);
// $mockedFavorites = $dataProvider->getModels();
// $pages = $dataProvider->getPagination();

// Регистрируем CSS - оставляем, может понадобиться для общих стилей страницы
$this->registerCssFile('@web/css/favorites.css'); // Возможно, переименовать или разделить

// Регистрируем JS для кнопки избранного
$this->registerJsFile(
    '@web/js/components/favorite-button.js',
    ['depends' => [\yii\web\JqueryAsset::class]] // Зависит от jQuery
);

// JS файл favorites.js больше не используется в этом представлении (удален из логов)
// $this->registerJsFile(
//     Yii::$app->request->baseUrl . '/js/favorites.js',
//     ['depends' => [\yii\web\JqueryAsset::class]]
// );
?>

<div class="favorites-page mb-5">

    <!-- <h1 class="h3 mb-4">Избранное и Подборки</h1> -->

    <!-- Навигация по вкладкам - теперь это ссылки -->
    <ul class="nav nav-tabs mb-4" id="favoritesTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $activeTab === 'favorites' ? 'active' : '' ?>" id="favorites-tab" href="<?= Url::to(['/favorites']) ?>" role="tab" aria-controls="favorites-content" aria-selected="<?= $activeTab === 'favorites' ? 'true' : 'false' ?>">
                <i class="fas fa-heart me-1"></i> Избранное
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $activeTab === 'collections' ? 'active' : '' ?>" id="collections-tab" href="<?= Url::to(['/collections']) ?>" role="tab" aria-controls="collections-content" aria-selected="<?= $activeTab === 'collections' ? 'true' : 'false' ?>">
                <i class="fas fa-folder-open me-1"></i> Подборки
            </a>
        </li>
    </ul>

    <!-- Содержимое вкладок -->
    <div class="tab-content" id="favoritesTabsContent">
        <div class="tab-pane fade <?= $activeTab === 'favorites' ? 'show active' : '' ?>" id="favorites-content" role="tabpanel" aria-labelledby="favorites-tab">
            <?php
            // Виджет избранного отображается всегда, но виден только если вкладка активна
            echo FavoritesListWidget::widget([
                'showTitle' => false,
                'showPagination' => true,
                'viewType' => 'grid',
                'emptyText' => 'У вас пока нет избранных объявлений',
                'options' => ['class' => 'favorites-container'],
            ]);
            ?>
        </div>
        <div class="tab-pane fade <?= $activeTab === 'collections' ? 'show active' : '' ?>" id="collections-content" role="tabpanel" aria-labelledby="collections-tab">
            <?php
            // Виджет подборок отображается всегда, но виден только если вкладка активна
            echo CollectionsListWidget::widget([
                'showTitle' => false,
                'showPagination' => true,
                'pageSize' => 10,
                'emptyText' => 'У вас пока нет созданных подборок',
                'options' => ['class' => 'collections-container'],
            ]);
            ?>
        </div>
    </div>

</div>

<?php
// Блок PHP с определением $js и registerJs УДАЛЕН
?>