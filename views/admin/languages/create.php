<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Languages $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Languages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="languages-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
