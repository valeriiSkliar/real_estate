<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TelegramChats $model */

$this->title = 'Create Telegram Chats';
$this->params['breadcrumbs'][] = ['label' => 'Telegram Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="telegram-chats-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
