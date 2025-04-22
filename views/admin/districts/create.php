<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Districts $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Districts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="districts-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
