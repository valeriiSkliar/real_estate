<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Advertisements $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Advertisements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advertisements-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
