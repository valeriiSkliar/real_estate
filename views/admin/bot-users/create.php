<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BotUsers $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи бота', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bot-users-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
