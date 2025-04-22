<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tariffs $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Тарифы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tariffs-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
