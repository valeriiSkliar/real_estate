<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Buttons $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Кнопки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buttons-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
