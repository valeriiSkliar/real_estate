<?php

use app\enums\Tariff;
use app\models\Promocodes;
use app\models\Tariffs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\PromocodesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Промокоды';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promocodes-index">
    <p>
        <?= Html::a('Создать новый', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'code',
            [
                'attribute' => 'tariff_id',
                'contentOptions' => [
                    'style' => ['width' => '150px', 'word-break' => 'break-all']
                ],
                'value' => function ($model) {
                    return Tariff::getTariffName((int) $model->tariff_id) ?: 'Не установлено'  ;
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'tariff_id',
                        'data' => Tariff::getTariffs(),
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            //'used_count',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Promocodes $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
