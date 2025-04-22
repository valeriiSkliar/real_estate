<?php

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => 'goanytime',
    'brandUrl'   => Yii::$app->homeUrl,
    'options'    => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'],
    'innerContainerOptions' => ['class' => 'container-fluid'],
]);
$menuItems = [];

// Add mobile sidebar toggle button
echo Html::button('<i class="fas fa-bars"></i>', [
    'class' => 'navbar-toggler d-md-none mobile-sidebar-toggle',
    'id' => 'mobileSidebarToggle',
    'aria-label' => 'Toggle navigation',
]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav ms-auto'],
    'items'   => $menuItems,
]);
NavBar::end();

// Include the mobile sidebar
echo $this->render('@app/views/layouts/frontend/partials/sidebar');
?>