<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Buttons $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Кнопки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="buttons-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
