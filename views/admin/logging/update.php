<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Logging $model */

$this->title = 'Update Logging: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Loggings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logging-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
