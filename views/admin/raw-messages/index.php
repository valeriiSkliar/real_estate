<?php

use app\enums\Source;
use app\models\RawMessages;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\RawMessagesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Raw Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="raw-messages-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
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
                'attribute' => 'text',
                'value' => function ($model) {
                    return StringHelper::truncate($model->text, 50);
                },
            ],
            'author',
            'chat_id',
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
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
