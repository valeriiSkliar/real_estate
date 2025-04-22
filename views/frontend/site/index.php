<?php

/** @var yii\web\View $this */

#use yii\helpers\Html;
use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\data\Pagination;
use yii\helpers\Url;
use app\models\MockPropertyData;

$this->title = 'goanytime.ru';
$foundCount = 9237;

// Create a pagination object for demonstration
$pages = new Pagination([
    'totalCount' => $foundCount,
    'pageSize' => 10,
    'pageSizeParam' => false,
]);
?>
<h1 class="h5 mb-6">Продажа объектов в г. Краснодар</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="mb-0 h6">Найдено <?= $foundCount ?> объектов</p>
  <div class="d-flex">

  <?= Html::button('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="funnel-icon filter-funnel"><path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z"/></svg>', [
        'class' => 'btn btn-primary p-2 mr-4',
        'id' => 'mobileSidebarToggle',
        'data-bs-toggle' => 'offcanvas',
        'data-bs-target' => '#mobileSidebar',
        'aria-controls' => 'mobileSidebar',
    ]) ?>
    
    <div class="dropdown sort-dropdown">
        <button class="btn btn-primary p-2 dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sort-icon sort-asc"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="sortDropdown">
            <li><a class="dropdown-item sort-option" href="#" data-sort="price" data-direction="asc">Цена (по возрастанию)</a></li>
            <li><a class="dropdown-item sort-option" href="#" data-sort="price" data-direction="desc">Цена (по убыванию)</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item sort-option" href="#" data-sort="date" data-direction="asc">Дата (сначала старые)</a></li>
            <li><a class="dropdown-item sort-option" href="#" data-sort="date" data-direction="desc">Дата (сначала новые)</a></li>
        </ul>
    </div>
  </div>
</div>

<div class="property-grid">
<?php
$properties = MockPropertyData::getProperties(10);

foreach ($properties as $property) {
    echo $this->render('//widgets/property-card-simple', [
        'id' => $property['id'],
        'price' => $property['price'],
        'pricePerSquareMeter' => $property['pricePerSquareMeter'],
        'address' => $property['title'],
        'location' => $property['address'],
        'detailUrl' => $property['detailUrl']
    ]);
}
?>
</div>

<div class="d-flex justify-content-center mt-4 mb-5">
    <?= LinkPager::widget([
        'pagination' => $pages,
        'options' => ['class' => 'pagination'],
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
        'maxButtonCount' => 5,
        'prevPageLabel' => '<span aria-hidden="true">&laquo;</span>',
        'nextPageLabel' => '<span aria-hidden="true">&raquo;</span>',
    ]) ?>
</div>

<style>
.sort-dropdown .dropdown-toggle::after {
    display: none;
}
.sort-dropdown .dropdown-menu {
    min-width: 200px;
}

.property-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
}

@media (max-width: 768px) {
    .property-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

.mr-4 {
    margin-right: 1rem;
}

.dropdown-menu-right {
    right: 0;
    left: auto;
}

.pagination .page-link {
    color: #333;
}

.pagination .page-item {
  margin: 0 3px;
  border-radius: 5px;
}

.pagination .page-item.disabled .page-link {
    border: none;
    background-color: #e2e2e2;
}

.pagination .page-item:not(.active) {
    border: 1px solid #ccc;
}

.pagination .page-item:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.pagination .page-item:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary);
    border-color: var(--primary);
    color: #fff;
}
</style>