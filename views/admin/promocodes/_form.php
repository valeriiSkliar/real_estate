<?php

use app\enums\Tariff;
use app\models\Tariffs;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Promocodes $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="promocodes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'payment_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tariff_id')->widget(Select2::class, [
        'data' => Tariff::getTariffs(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <?= $form->field($model, 'expire_at')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Выберите дату ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
