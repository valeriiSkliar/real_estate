<?php

use app\enums\ActiveStatuses;
use app\models\Districts;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\DistrictsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Район';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="districts-index">

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'slug',
            'name',
            [
                'attribute' => 'city_id',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return $model->tariff?->name;
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'city_id',
                        'data' => ArrayHelper::map(\app\models\Tariffs::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            'order',
            [
                'attribute' => 'status',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return ActiveStatuses::getLabel($model->status);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'status',
                        'data' => ActiveStatuses::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Districts $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
