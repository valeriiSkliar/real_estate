<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SourceChats $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Source Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="source-chats-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
