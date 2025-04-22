<?php

use app\models\BotUsers;
use app\models\Referrals;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\ReferralsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Реферальная программа';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="referrals-index">
    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Экспорт рефералы', ['/admin/referrals/export-referral'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'parent_id',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->parent_id, ['/admin/bot-users/update', 'id' => $model->parent_id]);
                }
            ],
            [
                'attribute' => 'referral_id',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->referral_id, ['/admin/bot-users/update', 'id' => $model->referral_id]);
                }
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
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Referrals $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
