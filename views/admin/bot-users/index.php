<?php

use app\enums\Tariff;
use app\models\BotUsers;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\BotUsersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи бота';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bot-users-index">
<!--    <p>Оповещения на всю базу: </p>-->
<!--    <a href="notification?value=1">-->
<!--        <button class="btn btn-success my-2">Включить</button>-->
<!--    </a>-->
<!--    <a href="notification?value=0">-->
<!--        <button class="btn btn-danger my-2">Выключить</button>-->
<!--    </a>-->

<!--    <div class="bot-users-days-filter">-->
<!--        <p>Введите количество дней:</p>-->
<!--        --><?php //= Html::beginForm(['filter-days'], 'post', ['data-pjax' => 1]) ?>
<!--        --><?php //= Html::input('number', 'days', '', ['class' => 'form-control', 'style' => 'width:100px; display:inline-block;', 'placeholder' => 'Дни']) ?>
<!--        --><?php //= Html::submitButton('Выдать триал', [
//            'class' => 'btn btn-primary',
//            'title' => 'Выдаст триал всем, у кого нет активного тарифа и триала, кто не является чьим-то рефералом',
//        ]) ?>
<!--        --><?php //= Html::endForm() ?>
<!--    </div>-->

    <p></p>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
//                'filterInputOptions' => [
//                    'class' => 'form-control',
//                    'style' => 'width:50px',
//                ],
//                'contentOptions' => [
//                    'style' => ['width' => '40px;']
//                ],
            ],
//            [
//                'attribute' => 'image',
//                'format' => 'raw',
//                'value' => function ($model) {
//                    return ($model->image) ? Html::img(trim(Yii::$app->params['url'], '/') . $model->image, ['width' => '100']) : null;
//                },
//            ],
            [
                'attribute' =>  'uid',
//                'contentOptions' => [
//                    'style' => ['width' => '120px', 'word-break' => 'break-all']
//                ],
            ],
//            [
//                'attribute' =>  'email',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
//            ],
            [
                'attribute' =>  'payment_email',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
            ],
            [
                'attribute' =>  'username',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
            ],
            [
                'attribute' =>  'fio',
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
            ],

//            'created_at',
//            'phone',
//            [
//                'attribute' => 'role_id',
//                'value' => function ($model) {
//                    return $model->role_id ? 'Админ' : 'Пользователь';
//                },
//                'filter' => Select2::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'role_id',
//                    'data' => ['0' => 'Пользователь', '1' => 'Админ'],
//                    'options' => ['placeholder' => '-'],
//                    'language' => 'ru',
//                    'pluginOptions' => [
//                        'allowClear' => true,
//                    ],
//                ]),
//            ],
//            [
//                'attribute' => 'notification_on',
//                'value' => function ($model) {
//                    return $model->notification_on ? 'Включено' : 'Выключено';
//                },
//                'filter' => Select2::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'notification_on',
//                    'data' => ['0' => 'Выключено', '1' => 'Включено'],
//                    'options' => ['placeholder' => '-'],
//                    'language' => 'ru',
//                    'pluginOptions' => [
//                        'allowClear' => true,
//                    ],
//                ]),
//            ],

//            [
//                'attribute' => 'language',
//                'filterInputOptions' => [
//                    'class' => 'form-control',
//                    'style' => 'width:50px',
//                ],
//                'contentOptions' => [
//                    'style' => ['width' => '40px;']
//                ],
//            ],
            [
                'attribute' => 'is_paid',
                'label'     => 'Бот оплачен?',
                'value'     => function ($model) {
                    return ($model->is_paid) ? 'Да' : 'Нет';
                },
                'filter'    => [0 => 'Нет', 1 => 'Да'],
//                'contentOptions' => [
//                    'style' => ['width' => '50px', 'word-break' => 'break-all']
//                ],
            ],
//            [
//                'attribute' => 'tariff',
//                'value'     => function ($model) {
//                    return Tariff::getTariffName($model->tariff);
//                },
//
//                'filter' => Select2::widget(
//                    [
//                        'model'         => $searchModel,
//                        'attribute'     => 'tariff',
//                        'data'          => Tariff::getTariffs(),
//                        'options'       => ['placeholder' => '-'],
//                        'language'      => 'ru',
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ],
//                    ]
//                ),
//                'contentOptions' => [
//                    'style' => ['width' => '150px', 'word-break' => 'break-all']
//                ],
//            ],
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
//                'contentOptions' => [
//                    'style' => ['width' => '180px', 'word-break' => 'break-all']
//                ],
            ],
//            [
//                'attribute' => 'trial_until',
//                'filter'    => DatePicker::widget([
//                    'model'         => $searchModel,
//                    'attribute'     => 'trial_until_start',
//                    'attribute2'    => 'trial_until_end',
//                    'type'          => DatePicker::TYPE_RANGE,
//                    'separator'     => '-',
//                    'pluginOptions' => [
//                        'todayHighlight' => true,
//                        'autoclose'      => true,
//                        'format'         => 'yyyy-mm-dd',
//                    ],
//                ]),
//                'format'    => ['datetime', 'php:Y-m-d H:i:s'],
//                'contentOptions' => [
//                    'style' => ['width' => '180px', 'word-break' => 'break-all']
//                ],
//            ],
//            [
//                'attribute' => 'last_visited_at',
//                'filter'    => DatePicker::widget([
//                    'model'         => $searchModel,
//                    'attribute'     => 'last_visited_at_start',
//                    'attribute2'    => 'last_visited_at_end',
//                    'type'          => DatePicker::TYPE_RANGE,
//                    'separator'     => '-',
//                    'pluginOptions' => [
//                        'todayHighlight' => true,
//                        'autoclose'      => true,
//                        'format'         => 'yyyy-mm-dd',
//                    ],
//                ]),
//                'format'    => ['datetime', 'php:Y-m-d H:i:s'],
//                'contentOptions' => [
//                    'style' => ['width' => '180px', 'word-break' => 'break-all']
//                ],
//            ],
//            [
//                'attribute' => 'Сделать админом',
//                'value' => function (BotUsers $model) {
//                    if ($model->role_id == 0) {
//                        return Html::a('<button class = "btn btn-primary btn-sm d-flex mx-auto">Сделать админом</button>', ['to-admin', 'id' => $model->id,], ['data-method' => 'post']);
//                    } else {
//                        return Html::a('<button class = "btn btn-success btn-sm d-flex mx-auto">Вернуть</button>', ['to-user', 'id' => $model->id,], ['data-method' => 'post']);
//                    }},
//                'format' => 'raw',
//            ],
//            [
//                'attribute' => 'Диалог',
//                'value' => function (BotUsers $model) {
//                    if ($model->on_call) {
//                        return Html::a('<button class = "btn btn-danger btn-sm mx-auto">Отключить</button>', ['disconnect-call', 'id' => $model->id,], ['data-method' => 'post']);
//                    }
//                    return 'Не в диалоге';
//                },
//                'format' => 'raw',
//            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, BotUsers $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
