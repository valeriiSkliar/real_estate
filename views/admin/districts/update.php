<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Districts $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Districts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="districts-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
