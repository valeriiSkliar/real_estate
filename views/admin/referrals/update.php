<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Referrals $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Referrals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="referrals-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
