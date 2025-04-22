<?php

use app\enums\PaymentStatuses;
use app\models\Payments;
use app\models\Tariffs;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */

$this->title = 'Выгрузки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="export-common-form">

    <h3>Экспорт общих данных</h3>

    <?= Html::beginForm(['admin/export/common'], 'post') ?>

    <div class="form-group">
        <div>
        <label for="date">Дата от (считает от 00:00:00)</label>
        <?= DatePicker::widget([
            'name' => 'date-from',
            'options' => ['placeholder' => 'Выберите дату...', 'id' => 'date-from'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
            ]
        ]); ?>
        </div>
        <div>
        <label for="date">Дата до (считает до 00:00:00)</label>
        <?= DatePicker::widget([
            'name' => 'date-to',
            'options' => ['placeholder' => 'Выберите дату...', 'id' => 'date-to'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
            ]
        ]); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="status">Статус</label>
        <?= Select2::widget([
            'name' => 'status[]',
            'data' => PaymentStatuses::getPaymentStatuses(),
            'options' => [
                'placeholder' => '-',
                'id' => 'status',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); ?>
    </div>

    <div class="form-group">
        <label for="tariff_id">Тариф</label>
        <?= Select2::widget([
            'name' => 'tariff_id[]',
            'data' => ArrayHelper::map(Tariffs::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            'options' => [
                'placeholder' => '-',
                'id' => 'tariff_id',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Экспортировать', ['class' => 'btn btn-warning']) ?>
    </div>

    <?= Html::endForm() ?>

</div>
