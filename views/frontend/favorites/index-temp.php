<?php

/* @var $this yii\web\View */

$this->title = 'Избранное';
?>

<div class="favorites-page">
    <div class="container">
        <h1 class="page-title"><?= $this->title ?></h1>

        <div class="favorites-list">
            <?php if (empty($favorites)): ?>
                <div class="empty-state">
                    <p>У вас пока нет избранных объявлений</p>
                </div>
            <?php else: ?>
                <?php foreach ($favorites as $favorite): ?>
                    <div class="favorite-item" data-id="<?= $favorite->id ?>">
                        <!-- Здесь будет карточка объявления -->
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>