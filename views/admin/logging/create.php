<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Logging $model */

$this->title = 'Create Logging';
$this->params['breadcrumbs'][] = ['label' => 'Loggings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logging-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
