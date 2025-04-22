<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AdvertisementSections $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Advertisement Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="advertisement-sections-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
