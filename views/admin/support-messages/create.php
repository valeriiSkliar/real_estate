<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SupportMessages $model */

$this->title = 'Create Support Messages';
$this->params['breadcrumbs'][] = ['label' => 'Support Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="support-messages-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
