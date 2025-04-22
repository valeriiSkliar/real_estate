<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserChat $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'User Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-chat-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
