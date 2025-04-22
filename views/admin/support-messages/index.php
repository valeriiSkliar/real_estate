<?php

use app\enums\SupportStatuses;
use app\models\SupportMessages;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\SupportMessagesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Support Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="support-messages-index">

    <p>
        <?= Html::a('Create Support Messages', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'user_id',
            [
                'attribute' => 'text',
                'value' => function ($model) {
                    return StringHelper::truncate($model->text, 50);
                },
            ],
            [
                'attribute' => 'status',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return SupportStatuses::getLabel($model->status);
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'status',
                        'data' => SupportStatuses::getAllCases(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],

            [
                'attribute' => 'created_at',
                'filter'    => DatePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'created_at_start',
                    'attribute2'    => 'created_at_end',
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
                'urlCreator' => function ($action, SupportMessages $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
