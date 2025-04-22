<?php

use app\enums\SendsDestinationTypes;
use app\enums\SendsProviderTypes;
use app\models\Languages;
use app\models\Sends;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SendsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Рассылка';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sends-index">

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
            'title',
            [
                'attribute' => 'destination',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function($model) {
                    $destinationIntegers = explode(',', $model->destination);
                    $destinationLabels = [];

                    foreach ($destinationIntegers as $destinationInteger) {
                        $destinationLabels[] = SendsDestinationTypes::getLabel((int) $destinationInteger);
                    }

                    return implode(PHP_EOL, $destinationLabels);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'destination',
                        'data' => SendsDestinationTypes::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
//            [
//                'attribute' => 'provider',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
//                'value' => function ($model) {
//                    return SendsProviderTypes::getLabel((int) $model->provider);
//                },
//
//                'filter' => Select2::widget(
//                    [
//                        'model' => $searchModel,
//                        'attribute' => 'provider',
//                        'data' => SendsProviderTypes::getAllCases(),
//                        'options' => ['placeholder' => '-'],
//                        'language' => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//            ],
            [
                'attribute' => 'date',
                'filter'    => DatePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'date_start',
                    'attribute2'    => 'date_end',
                    'type'          => DatePicker::TYPE_RANGE,
                    'separator'     => '-',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose'      => true,
                        'format'         => 'yyyy-mm-dd',
                    ],
                ]),
                'format'    => ['datetime', 'php:Y-m-d H:i:s'],
            ],
//            [
//                'attribute' => 'language',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
//                'value' => function ($model) {
//                    return $model->language;
//                },
//
//                'filter' => Select2::widget(
//                    [
//                        'model' => $searchModel,
//                        'attribute' => 'language',
//                        'data' => ArrayHelper::map(
//                                Languages::find()->orderBy(['name' => SORT_ASC])->all(),
//                                'slug',
//                                'name'
//                        ),
//                        'options' => ['placeholder' => '-'],
//                        'language' => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//            ],
//            [
//                'attribute' => 'is_regular',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
//                'value' => function ($model) {
//                    return $model->is_regular ? 'Регулярно' : 'Не регулярно' ;
//                },
//
//                'filter' => Select2::widget(
//                    [
//                        'model' => $searchModel,
//                        'attribute' => 'is_regular',
//                        'data' => [0 => 'Не регулярно', 1 => 'Регулярно'],
//                        'options' => ['placeholder' => '-'],
//                        'language' => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//            ],
            [
                'attribute' => 'status',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return Sends::$STATUSES[$model->status]; ;
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'status',
                        'data' => Sends::$STATUSES,
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'class' => ActionColumn::class,
                'template' => ' {update} {delete} ',
                'urlCreator' => function ($action, Sends $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
