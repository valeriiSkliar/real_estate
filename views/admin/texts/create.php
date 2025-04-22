<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Texts $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Texts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="texts-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
