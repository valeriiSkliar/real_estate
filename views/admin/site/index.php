<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'goanytime bot';
?>
<div class="site-index">
    <h1>Приветствуем в админ панели</h1>
    <?= Html::a('Очистить кэш', ['/admin/queue/cache-clear'], ['class' => 'btn btn-danger my-3 mx-3']) ?>
</div>
