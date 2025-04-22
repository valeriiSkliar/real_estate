<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Texts $model */

$this->title = 'Редактировать: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Тексты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="texts-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
