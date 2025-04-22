<?php

/** @var yii\web\View $this */

use app\models\Disease;

$this->title = 'Cimmetria bot';

$d = Disease::findOne(22);
echo $d->description;
?>
<div class="site-index">
    <h1>Приветствуем в админ панели</h1>
</div>
