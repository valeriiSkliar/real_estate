<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ButtonClicks $model */

$this->title = 'Update Button Clicks: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Button Clicks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="button-clicks-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
