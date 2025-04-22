<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AdvertisementSections $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Advertisement Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advertisement-sections-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
