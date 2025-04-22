<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Selections $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Selections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="selections-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
