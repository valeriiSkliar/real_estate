<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */

$this->title = 'Создать рассылку';
$this->params['breadcrumbs'][] = ['label' => 'Ручная рассылка', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sends-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
