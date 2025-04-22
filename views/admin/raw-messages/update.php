<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RawMessages $model */

$this->title = 'Update Raw Messages: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Raw Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="raw-messages-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
