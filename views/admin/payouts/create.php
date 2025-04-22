<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Payouts $model */

$this->title = 'Create Payouts';
$this->params['breadcrumbs'][] = ['label' => 'Payouts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payouts-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
