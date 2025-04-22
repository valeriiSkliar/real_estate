<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Pages $model */

$this->title = $model->h1;
$this->params['breadcrumbs'][] = ['label' => 'Страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="page-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'command',
            'h1',
            'text:ntext',
            [
                'attribute' => 'Изображение',
                'value' => function ($model){
                    return  $model->image ? Html::img(($model->image),
                        ['class' => 'img-thumbnail']) : null;
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 500px'],
            ],
        ],
    ]) ?>

</div>
