<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RawMessages $model */

$this->title = 'Create Raw Messages';
$this->params['breadcrumbs'][] = ['label' => 'Raw Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="raw-messages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
