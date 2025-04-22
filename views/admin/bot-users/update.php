<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BotUsers $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи бота', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="bot-users-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
