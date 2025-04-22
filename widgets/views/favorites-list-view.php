<?php
/**
 * Представление для виджета списка избранного
 * 
 * @var yii\data\ArrayDataProvider $dataProvider Провайдер данных
 * @var string $title Заголовок виджета
 * @var bool $showTitle Показывать ли заголовок
 * @var bool $showPagination Показывать ли пагинацию
 * @var string $viewType Вид отображения (grid, list)
 * @var array $options Дополнительные HTML-атрибуты для контейнера
 * @var string $emptyText Текст для пустого списка
 */

use yii\helpers\Html;
use yii\bootstrap5\LinkPager;
use yii\helpers\Url;

// Получаем модели для текущей страницы
$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
?>

<div <?= Html::renderTagAttributes($options) ?>>
    <?php if ($showTitle): ?>
        <h2 class="h4 mb-4"><?= Html::encode($title) ?></h2>
    <?php endif; ?>
    
    <?php if (empty($models)): ?>
        <div class="empty-favorites-message text-center py-5">
            <i class="fas fa-heart text-muted" style="font-size: 48px;"></i>
            <h3 class="mt-3"><?= Html::encode($emptyText) ?></h3>
            <p class="text-muted mb-4">Добавляйте понравившиеся объявления в избранное, чтобы они отображались здесь</p>
            <a href="<?= Url::to(['/']) ?>" class="btn btn-primary">Перейти к поиску объявлений</a>
        </div>
    <?php else: ?>
        <div class="property-grid" id="favorites-grid">
            <?php foreach ($models as $property): ?>
                <div class="favorite-item" data-property-id="<?= $property['id'] ?>">
                    <div class="property-card">
                        <div class="card-header">
                            <h3 class="price"><?= Html::encode($property['price']) ?></h3>
                            <div class="price-per-meter"><?= Html::encode($property['pricePerSquareMeter']) ?></div>
                        </div>

                        <div class="card-body">
                            <h4 class="address"><?= Html::encode($property['title']) ?></h4>
                            <div class="location"><?= Html::encode($property['address']) ?></div>
                        </div>

                        <div class="card-footer">
                            <div class="action-buttons">
                                <a href="<?= $property['detailUrl'] ?>" class="btn-link btn btn-primary" title="Подробнее">
                                Фото и подробности
                                </a>

                                <?= app\widgets\FavoriteButtonWidget::widget([
                                    'propertyId' => $property['id'],
                                    'isFavorite' => true,
                                    'buttonClass' => 'btn-icon favorite-toggle-btn',
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
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($showPagination && $pagination->pageCount > 1): ?>
            <div class="d-flex justify-content-center mt-4 mb-5">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                    'maxButtonCount' => 5,
                    'prevPageLabel' => '<span aria-hidden="true">&laquo;</span>',
                    'nextPageLabel' => '<span aria-hidden="true">&raquo;</span>',
                ]) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Всплывающие уведомления -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="favoriteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Уведомление</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Объявление удалено из избранного
        </div>
    </div>
</div>
