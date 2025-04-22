<?php

use app\enums\PropertyTypes;
use app\models\Advertisements;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\AdvertisementsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Объявления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advertisements-index">

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
            [
                'attribute' => 'property_type',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return PropertyTypes::getLabel($model->property_type);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'property_type',
                        'data' => PropertyTypes::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'attribute' => 'trade_type',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return \app\enums\TradeTypes::getLabel($model->trade_type);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'trade_type',
                        'data' => \app\enums\TradeTypes::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'attribute' => 'source',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return \app\enums\Source::getLabel($model->trade_type);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'source',
                        'data' => \app\enums\Source::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            'realtor_phone',
            //'address',
            //'clean_description:ntext',
            //'raw_description:ntext',
            //'property_name',
            'locality',
            //'district',
            //'room_quantity',
            //'property_area',
            //'land_area',
            //'condition',
            //'price',
            //'created_at',
            [
                'attribute' => 'updated_at',
                'filter'    => DatePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'updated_at_start',
                    'attribute2'    => 'updated_at_end',
                    'type'          => DatePicker::TYPE_RANGE,
                    'separator'     => '-',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose'      => true,
                        'format'         => 'yyyy-mm-dd',
                    ],
                ]),
                'format'    => ['datetime', 'php:Y-m-d H:i:s'],
                'contentOptions' => [
                    'style' => ['width' => '180px', 'word-break' => 'break-all']
                ],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Advertisements $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
