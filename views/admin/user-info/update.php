<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserInfo $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-info-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
