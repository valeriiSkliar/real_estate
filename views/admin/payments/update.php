<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="payments-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
