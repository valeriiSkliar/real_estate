<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserInfo $model */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'User Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
