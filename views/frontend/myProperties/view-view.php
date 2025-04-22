<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Advertisements */
/* @var $images app\models\AdvertisementImages[] */

$this->title = 'Объявление #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Объявления', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Labels for property details
$propertyTypeLabels = [
    'apartment' => 'Квартира',
    'house' => 'Дом',
    'land' => 'Участок',
];

$tradeTypeLabels = [
    'sale' => 'Продажа',
    'rent' => 'Аренда',
];

$conditionLabels = [
    'new' => 'Новое',
    'good' => 'Хорошее',
    'needs_repair' => 'Требует ремонта',
];
?>

<div class="mobile-advertisement-view">
    <div class="mobile-header sticky-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-2">
                <?= Html::a('<i class="fas fa-arrow-left"></i>', ['index'], ['class' => 'btn btn-link text-dark p-0']) ?>
                <h1 class="h5 mb-0"><?= Html::encode($this->title) ?></h1>
                <div class="dropdown">
                    <button class="btn btn-link text-dark p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li>
                            <?= Html::a('<i class="fas fa-edit me-2"></i> Редактировать', ['update', 'id' => $model->id], ['class' => 'dropdown-item']) ?>
                        </li>
                        <li>
                            <?= Html::a('<i class="fas fa-trash-alt me-2"></i> Удалить', ['delete', 'id' => $model->id], [
                                'class' => 'dropdown-item text-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите удалить это объявление?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <!-- Images Carousel -->
        <?php if (!empty($images)): ?>
        <div class="card mb-3">
            <div id="adImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($images as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="image-container">
                            <img src="<?= Yii::getAlias('@web/uploads/advertisements/' . $image->image) ?>" class="d-block w-100" alt="Изображение <?= $index + 1 ?>">
                            <div class="image-counter">
                                <?= ($index + 1) ?> / <?= count($images) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#adImagesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Предыдущий</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#adImagesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Следующий</span>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Price and Main Info -->
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="price-label"><?= number_format($model->price, 0, '.', ' ') ?> ₽</h2>
                <p class="address-label"><?= Html::encode($model->address) ?></p>
                
                <div class="property-badges mt-2 mb-3">
                    <span class="badge bg-primary"><?= $tradeTypeLabels[$model->trade_type] ?? $model->trade_type ?></span>
                    <span class="badge bg-secondary"><?= $propertyTypeLabels[$model->property_type] ?? $model->property_type ?></span>
                    <?php if ($model->condition): ?>
                    <span class="badge bg-info"><?= $conditionLabels[$model->condition] ?? $model->condition ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="property-details">
                    <?php if ($model->property_area): ?>
                    <div class="detail-item">
                        <i class="fas fa-home"></i>
                        <span><?= $model->property_area ?> м²</span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($model->room_quantity): ?>
                    <div class="detail-item">
                        <i class="fas fa-door-open"></i>
                        <span><?= $model->room_quantity ?> <?= \yii\helpers\Inflector::pluralize('комната', $model->room_quantity) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($model->land_area): ?>
                    <div class="detail-item">
                        <i class="fas fa-chart-area"></i>
                        <span><?= $model->land_area ?> м² участок</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Description -->
        <?php if ($model->clean_description): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Описание</h5>
            </div>
            <div class="card-body">
                <div class="description-text">
                    <?= nl2br(Html::encode($model->clean_description)) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Raw Description -->
        <?php if ($model->raw_description): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Исходное описание</h5>
            </div>
            <div class="card-body">
                <div class="description-text">
                    <?= nl2br(Html::encode($model->raw_description)) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Location and Details -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Расположение и детали</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <?php if ($model->district): ?>
                    <dt class="col-5">Район:</dt>
                    <dd class="col-7"><?= Html::encode($model->district) ?></dd>
                    <?php endif; ?>
                    
                    <?php if ($model->city): ?>
                    <dt class="col-5">Город:</dt>
                    <dd class="col-7"><?= Html::encode($model->city->name) ?></dd>
                    <?php endif; ?>
                    
                    <?php if ($model->realtor_phone): ?>
                    <dt class="col-5">Контакт:</dt>
                    <dd class="col-7">
                        <?= Html::a(
                            Html::encode($model->realtor_phone),
                            'tel:' . $model->realtor_phone,
                            ['class' => 'text-decoration-none']
                        ) ?>
                    </dd>
                    <?php endif; ?>
                    
                    <dt class="col-5">Добавлено:</dt>
                    <dd class="col-7"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></dd>
                    
                    <?php if ($model->updated_at && $model->updated_at != $model->created_at): ?>
                    <dt class="col-5">Обновлено:</dt>
                    <dd class="col-7"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <div class="d-grid gap-2 mb-5 pb-5">
            <?= Html::a('Вернуться к списку', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>
</div>

<!-- Fixed bottom action bar for contact -->
<?php if ($model->realtor_phone): ?>
<div class="fixed-bottom bg-white border-top p-3 action-bar">
    <div class="container">
        <div class="row g-2">
            <div class="col">
                <a href="tel:<?= $model->realtor_phone ?>" class="btn btn-success w-100">
                    <i class="fas fa-phone-alt me-2"></i> Позвонить риэлтору
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .mobile-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
        z-index: 1030;
    }
    
    .action-bar {
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 1020;
    }
    
    /* Space at the bottom to prevent content from being hidden behind the action bar */
    .mb-5.pb-5 {
        margin-bottom: 5rem !important;
    }
    
    /* Image container for carousel */
    .image-container {
        position: relative;
        width: 100%;
        height: 300px;
        background-color: #f8f9fa;
    }
    
    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-counter {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: rgba(0,0,0,0.5);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.8rem;
    }
    
    /* Price and details styling */
    .price-label {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .address-label {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .property-details {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 1rem;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #495057;
    }
    
    .detail-item i {
        color: #6c757d;
    }
    
    /* Description styling */
    .description-text {
        white-space: pre-line;
        color: #495057;
    }
    
    /* Property badges */
    .property-badges .badge {
        margin-right: 5px;
    }
</style>
