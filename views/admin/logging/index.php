<?php

use app\models\Logging;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\LoggingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Loggings';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .container {
        margin-left: 10% !important;
        margin-right: 5% !important;
    }
</style>
<div class="logging-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width: 50px; white-space: nowrap;'],
            ],
            'user_id',
            'created_at',
            [
                'attribute' => 'old',
                'contentOptions' => ['style' => 'width: 350px; white-space: nowrap;'],
            ],
            [
                'attribute' => 'new',
                'contentOptions' => ['style' => 'width: 350px; white-space: nowrap;'],
            ],
            [
                'attribute' => 'type',
                'contentOptions' => ['style' => 'width: 350px;'],
                'filter' => Logging::TYPE,
                'value' => function ($model) {
                    return Logging::TYPE[$model->type];
                },
                'format' => 'raw',
            ],
            [
                'attribute' =>'details',
                'contentOptions' => ['style' => 'width: 350px; white-space: nowrap;'],
            ],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
