<?php

use app\enums\ActiveStatuses;
use app\models\Tariffs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\TariffsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Тарифы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tariffs-index">
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
            'name',
            'price',
//            'description:ntext',
//            'currency',
//            'discount',
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
//                        'data' => ArrayHelper::map(\app\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
//                        'options' => ['placeholder' => '-'],
//                        'language' => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//            ],
//            [
//                'attribute' => 'status',
//                'contentOptions' => [
//                    'style' => ['width' => '250px', 'word-break' => 'break-all']
//                ],
//                'value' => function ($model) {
//                    return TariffStatuses::getLabel((int) $model->status);
//                },
//
//                'filter' => Select2::widget(
//                    [
//                        'model' => $searchModel,
//                        'attribute' => 'status',
//                        'data' => TariffStatuses::getAllCases(),
//                        'options' => ['placeholder' => '-'],
//                        'language' => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//            ],
            [
                'class' => ActionColumn::className(),
                'template' => ' {update} {delete} ',
                'urlCreator' => function ($action, Tariffs $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
