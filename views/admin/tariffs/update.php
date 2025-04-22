<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tariffs $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Тарифы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="tariffs-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
