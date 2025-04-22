<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Selections $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Selections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="selections-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
