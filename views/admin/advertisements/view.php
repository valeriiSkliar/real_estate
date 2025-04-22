<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Advertisements $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Advertisements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="advertisements-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'property_type',
            'trade_type',
            'source',
            'realtor_phone',
            'address',
            'clean_description:ntext',
            'raw_description:ntext',
            'property_name',
            'locality',
            'district',
            'room_quantity',
            'property_area',
            'land_area',
            'condition',
            'price',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
