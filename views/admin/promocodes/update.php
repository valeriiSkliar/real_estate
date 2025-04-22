<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Promocodes $model */

$this->title = 'Update Promocodes: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Promocodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="promocodes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
