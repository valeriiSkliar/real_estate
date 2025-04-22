<?php

use app\models\Pages;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\PagesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Страницы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">
    <p>
        <?= Html::a('Создать страницу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'command',
            'h1',
            [
                'attribute' => 'Изображение',
                'value' => function ($model){
                    return  $model->image ? Html::img(trim(Yii::$app->params['url'], '/') . $model->image, ['width' => '200']) : null;
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 100px'],
            ],
            [
                'attribute' => 'text',
                'format' => 'ntext',
                'value' => function ($model) {
                    return StringHelper::truncateWords($model->text, 20, '...', true);
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
                'urlCreator' => function ($action, Pages $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
