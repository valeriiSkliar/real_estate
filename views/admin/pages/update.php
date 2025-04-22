<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Pages $model */

$this->title = 'Редактировать страницу: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="page-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
