<?php

use app\models\Payouts;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\PayoutsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Payouts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payouts-index">
    <p>
        <?= Html::a('Начислить', ['create', 'status' => Payouts::STATUS_ADDED], ['class' => 'btn btn-success']) ?>

        <?= Html::a('Списать', ['create', 'status' => Payouts::STATUS_WITHDRAWN], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'telegram_id',
            'amount',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return Payouts::statusLabel($model->status);
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    Payouts::statusList(),
                    ['class' => 'form-control', 'prompt' => '  ']
                ),
                'format' => 'raw',
            ],
            [
                'label' => 'Списание',
                'format' => 'raw',
                'content' => function ($model) {
                    if ($model->status == Payouts::STATUS_DRAFT) {
                        return Html::a('Списать', ['withdraw', 'id' => $model->id], ['class' => 'btn btn-danger']);
                    } else {
                        return '';
                    }
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Payouts $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
