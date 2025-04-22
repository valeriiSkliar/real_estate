<?php

/**
 * Simple property card widget
 * 
 * @var string $price The property price (formatted)
 * @var string $pricePerSquareMeter Price per square meter (formatted)
 * @var string $address Full address of the property
 * @var string $location District, complex, city information
 * @var string $detailUrl URL to the property detail page
 * @var int $id Property ID
 */

use yii\helpers\Html;
?>

<div class="property-card" data-id="<?= $id ?>">
  <div class="card-header">
    <h3 class="price"><?= Html::encode($price) ?></h3>
    <div class="price-per-meter"><?= Html::encode($pricePerSquareMeter) ?></div>
  </div>

  <div class="card-body">
    <h4 class="address"><?= Html::encode($address) ?></h4>
    <div class="location"><?= Html::encode($location) ?></div>
  </div>

  <div class="card-footer">
    <div class="action-buttons">
      <a href="<?= $detailUrl ?>" class="btn-link btn btn-primary" title="Подробнее">
        Фото и подробности
      </a>

      <button type="button" class="btn-icon" title="Добавить в подбарку">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-notebook-tabs-icon lucide-notebook-tabs">
          <path d="M2 6h4" />
          <path d="M2 10h4" />
          <path d="M2 14h4" />
          <path d="M2 18h4" />
          <rect width="16" height="20" x="4" y="2" rx="2" />
          <path d="M15 2v20" />
          <path d="M15 7h5" />
          <path d="M15 12h5" />
          <path d="M15 17h5" />
        </svg>
      </button>

      <?= app\widgets\FavoriteButtonWidget::widget([
        'propertyId' => $id,
        'buttonClass' => 'btn-icon favorite-btn',
        'addTitle' => 'Добавить в избранное',
        'removeTitle' => 'Удалить из избранного',
      ]) ?>

      <button type="button" class="btn-icon info-btn" title="Пожаловаться">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle-warning-icon lucide-message-circle-warning">
          <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
          <path d="M12 8v4" />
          <path d="M12 16h.01" />
        </svg>
      </button>
    </div>
  </div>
</div>