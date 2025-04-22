<?php

use app\enums\ActiveStatuses;
use app\enums\PropertyTypes;
use app\enums\Source;
use app\models\SourceChats;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\SourceChatsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Парсер';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-chats-index">

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
            'chat_id',
            [
                'attribute' => 'platform',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return Source::getLabel($model->platform);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'platform',
                        'data' => Source::getPlatformCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'attribute' => 'active',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return ActiveStatuses::getLabel($model->active);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'active',
                        'data' => ActiveStatuses::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],

            //'stop_words',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, SourceChats $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
