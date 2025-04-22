<?php

use app\models\UserInfo;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\UserInfoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-index">
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'user_id',
            [
                'attribute' => 'photo_url',
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->photo_url) ? Html::img(trim(Yii::$app->params['url'], '/') . $model->photo_url, ['width' => '200']) : null;
                },
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'value' => function ($model) {
                    return StringHelper::truncateWords($model->description, 20, '...', true);
                },
            ],
            [
                'attribute' => 'language',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return $model->language;
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'language',
                        'data' => ArrayHelper::map(\app\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
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
                'template' => ' {update} {delete} ',
                'urlCreator' => function ($action, UserInfo $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
