<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Cities $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
