<?php

use app\models\Texts;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\TextsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Тексты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="texts-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'slug',
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return strlen($model->name) > 100 ? substr($model->name, 0, 100) . '...' : $model->name;
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
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Texts $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
