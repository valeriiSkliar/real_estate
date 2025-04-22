<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AdvertisementsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мои объявления';
$this->params['breadcrumbs'][] = $this->title;

// Mock data for demonstration
$mockAds = [
    [
        'id' => 1,
        'property_type' => 'apartment',
        'trade_type' => 'sale',
        'price' => 3500000,
        'address' => 'ул. Ленина, 10, кв. 45',
        'property_area' => 72,
        'room_quantity' => 3,
        'image' => 'sample1.jpg',
    ],
    [
        'id' => 2,
        'property_type' => 'house',
        'trade_type' => 'rent',
        'price' => 75000,
        'address' => 'ул. Пушкина, 23',
        'property_area' => 120,
        'room_quantity' => 4,
        'image' => 'sample2.jpg',
    ],
    [
        'id' => 3,
        'property_type' => 'apartment',
        'trade_type' => 'sale',
        'price' => 4200000,
        'address' => 'ул. Гагарина, 5, кв. 12',
        'property_area' => 65,
        'room_quantity' => 2,
        'image' => 'sample3.jpg',
    ],
];

// Property and trade type labels
$propertyTypeLabels = [
    'apartment' => 'Квартира',
    'house' => 'Дом',
    'land' => 'Участок',
];

$tradeTypeLabels = [
    'sale' => 'Продажа',
    'rent' => 'Аренда',
];
?>

<div class="mobile-advertisements-index">
    <div class="mobile-header sticky-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-2">
                <h1 class="h5 mb-0"><?= Html::encode($this->title) ?></h1>
                <?= Html::a('<i class="fas fa-plus"></i>', ['create'], [
                    'class' => 'btn btn-primary rounded-circle d-flex align-items-center justify-content-center',
                    'style' => 'width: 38px; height: 38px;',
                    'title' => 'Создать объявление',
                ]) ?>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <!-- Tab navigation for filtering -->
        <ul class="nav nav-pills nav-fill mb-3">
            <li class="nav-item">
                <a class="nav-link active" id="all-tab" data-bs-toggle="pill" href="#all">Все</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sale-tab" data-bs-toggle="pill" href="#sale">Продажа</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="rent-tab" data-bs-toggle="pill" href="#rent">Аренда</a>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="all">
                <!-- All advertisements -->
                <div class="advertisement-list">
                    <?php foreach ($mockAds as $ad): ?>
                        <?= $this->render('_ad_card', [
                            'ad' => $ad,
                            'propertyTypeLabels' => $propertyTypeLabels,
                            'tradeTypeLabels' => $tradeTypeLabels
                        ]) ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="sale">
                <!-- Sale advertisements -->
                <div class="advertisement-list">
                    <?php foreach ($mockAds as $ad): ?>
                        <?php if ($ad['trade_type'] === 'sale'): ?>
                            <?= $this->render('_ad_card', [
                                'ad' => $ad,
                                'propertyTypeLabels' => $propertyTypeLabels,
                                'tradeTypeLabels' => $tradeTypeLabels
                            ]) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="rent">
                <!-- Rent advertisements -->
                <div class="advertisement-list">
                    <?php foreach ($mockAds as $ad): ?>
                        <?php if ($ad['trade_type'] === 'rent'): ?>
                            <?= $this->render('_ad_card', [
                                'ad' => $ad,
                                'propertyTypeLabels' => $propertyTypeLabels,
                                'tradeTypeLabels' => $tradeTypeLabels
                            ]) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .mobile-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
        z-index: 1030;
    }

    .advertisement-list {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Nav pills styling */
    .nav-pills .nav-link {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .nav-pills .nav-link.active {
        background-color: var(--bs-primary);
    }
</style>