<?php

use app\enums\Tariff;
use app\models\BotUsers;
use app\models\TopUps;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BotUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statistic array */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;

$week = $statistic['week'];
$month = $statistic['month'];
$tariff = $statistic['tariff'];
?>

<div style="width:100%; height: auto" id="container">
    <div style="width:50%; float:left;" id='chart1'></div>
    <div style="width:50%; float:left;" id='chart2'></div>
    <div style="width:50%; float:left;" id='chart4'></div>
    <div style="width:50%; float:left;" id='chart5'></div>
</div>
<div class="bot-users-index">
    <p>
        <?= Html::a('Сбросить фильтры', ['/bot-users/statistic'], ['class' => 'btn btn-success']) ?>
    </p>
    <h1><?= Html::encode($this->title) ?></h1>


    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'username',
                'label'     => 'Username',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::a("@" . $model->username, "https://t.me/" . $model->username, ['target' => '_blank']);
                },
            ],
            'uid',
            [
                'attribute' => 'is_paid',
                'label'     => 'Бот оплачен?',
                'value'     => function ($model) {
                    return ($model->is_paid) ? 'Да' : 'Нет';
                },
                'filter'    => [0 => 'Нет', 1 => 'Да'],
            ],
            [
                'attribute' => 'tariff',
                'value'     => function ($model) {
                    return Tariff::getTariffName($model->tariff);
                },

                'filter' => Select2::widget(
                    [
                        'model'         => $searchModel,
                        'attribute'     => 'tariff',
                        'data'          => Tariff::getTariffs(),
                        'options'       => ['placeholder' => '-'],
                        'language'      => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'attribute' => 'paid_until',
                'filter'    => DatePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'paid_until_start',
                    'attribute2'    => 'paid_until_end',
                    'type'          => DatePicker::TYPE_RANGE,
                    'separator'     => '-',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose'      => true,
                        'format'         => 'yyyy-mm-dd',
                    ],
                ]),
                'format'    => ['datetime', 'php:Y-m-d H:i:s'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php
$data = [
    ['name' => 'Бизнес', 'y' => $tariff[Tariff::MONTH_3->value]],
    ['name' => 'Премиум', 'y' => $tariff[Tariff::MONTH_1->value]],
    ['name' => 'Автор', 'y' => $tariff[Tariff::MONTH_6->value]],
];

$data2 = [
    ['name' => '7 дней бизнес', 'y' => $week[Tariff::MONTH_3->value]],
    ['name' => '7 дней премиум', 'y' => $week[Tariff::MONTH_1->value]],
    ['name' => '7 дней автор', 'y' => $week[Tariff::MONTH_6->value]],
    ['name' => 'месяц бизнес', 'y' => $month[Tariff::MONTH_3->value]],
    ['name' => 'месяц премиум', 'y' => $month[Tariff::MONTH_1->value]],
    ['name' => 'месяц автор', 'y' => $month[Tariff::MONTH_6->value]],
];

?>

<script>
    window.addEventListener("load", (event) => {
        Highcharts.chart('chart1', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Подписка'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        distance: -50,
                        formatter: function () {
                            return '<b>' + this.y + '</b><br/>';
                        },
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'white'
                        }
                    },
                    showInLegend: true
                }
            },

            series: [{
                name: 'Values',
                colorByPoint: true,
                data: <?= json_encode($data) ?>

            }]
        });


        Highcharts.chart('chart4', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Подписка истекает через:'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        distance: -50,
                        formatter: function () {
                            return '<b>' + this.y + '</b><br/>';
                        },
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'white'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Values',
                colorByPoint: true,
                data: <?= json_encode($data2) ?>
            }]
        });
    });
</script>

<script src="https://code.highcharts.com/highcharts.js"></script>
