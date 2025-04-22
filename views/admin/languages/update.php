<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Languages $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Languages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="languages-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
