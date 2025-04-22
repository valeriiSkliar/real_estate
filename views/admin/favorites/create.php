<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Favorites $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Favorites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="favorites-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
