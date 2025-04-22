<?php

use app\models\Buttons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\ButtonsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Кнопки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buttons-index">

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
            'priority',
            [
                'attribute' => 'is_hidden',
                'filter' => Html::activeDropDownList($searchModel, 'is_hidden', Buttons::VISIBILITY,
                    ['class' => 'form-control', 'prompt' => 'Все']),
                'value' => function ($model) {
                    return Buttons::VISIBILITY[$model->is_hidden];
                },
            ],
//            [
//                'attribute' => 'type',
//                'filter' => Html::activeDropDownList($searchModel, 'type', Buttons::TYPES,
//                    ['class' => 'form-control', 'prompt' => 'Все']),
//                'value' => function ($model) {
//                    return Buttons::TYPES[$model->type];
//                },
//            ],
            [
                'attribute' => 'position',
                'filter' => Html::activeDropDownList($searchModel, 'position', Buttons::POSITIONS,
                    ['class' => 'form-control', 'prompt' => 'Все']),
                'value' => function ($model) {
                    return Buttons::POSITIONS[$model->position];
                },
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
//                        'data' => ArrayHelper::map(\app\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
//                        'options' => ['placeholder' => '-'],
//                        'language' => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Buttons $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
