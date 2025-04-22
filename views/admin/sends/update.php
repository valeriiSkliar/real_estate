<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sends', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sends-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
