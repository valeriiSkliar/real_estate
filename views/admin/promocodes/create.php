<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Promocodes $model */

$this->title = 'Create Promocodes';
$this->params['breadcrumbs'][] = ['label' => 'Promocodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promocodes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
