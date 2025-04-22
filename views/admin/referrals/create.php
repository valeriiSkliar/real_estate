<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Referrals $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Referrals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="referrals-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
