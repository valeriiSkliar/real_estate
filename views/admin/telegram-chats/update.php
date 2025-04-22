<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TelegramChats $model */

$this->title = 'Update Telegram Chats: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Telegram Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="telegram-chats-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
