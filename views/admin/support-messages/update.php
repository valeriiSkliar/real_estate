<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SupportMessages $model */

$this->title = 'Update Support Messages: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Support Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="support-messages-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
