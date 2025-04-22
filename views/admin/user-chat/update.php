<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserChat $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-chat-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
