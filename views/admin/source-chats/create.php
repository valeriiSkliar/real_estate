<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SourceChats $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Source Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-chats-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
