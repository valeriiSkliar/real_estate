<?php

use app\enums\Tariff;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BotUsers $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bot-users-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //= $form->field($model, 'password')->textInput()->label('Пароль (Оставьте пустым,если не хотите менять.)') ?>

    <?= $form->field($model, 'uid')->textInput() ?>

    <?= $form->field($model, 'is_paid')->dropDownList(
        ['1' => 'Да', '0' => 'Нет'],
        ['prompt' => '-']
    ); ?>

    <?= $form->field($model, 'tariff')->widget(Select2::class, [
        'data' => Tariff::getTariffs(),
        'options' => [
            'prompt'  => 'Выберите тариф...'
        ]
    ]) ?>

    <?= $form->field($model, 'paid_until')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Выберите дату ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]); ?>

<!--    --><?php //= $form->field($model, 'trial_until')->widget(DatePicker::class, [
//        'options' => ['placeholder' => 'Выберите дату ...'],
//        'pluginOptions' => [
//            'autoclose' => true,
//            'format' => 'yyyy-mm-dd'
//        ]
//    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
<!--    --><?php //= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
<!--    --><?php //= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>
<!--    --><?php //= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'payment_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role_id')->dropDownList(
        ['0' => 'Пользователь', '1' => 'Админ'],
        ['prompt' => '-']
    ); ?>
<!---->
<!--    --><?php //= $form->field($model, 'notification_on')->dropDownList(
//        ['1' => 'Включено', '0' => 'Выключено'],
//        ['prompt' => '-']
//    ); ?>
<!---->
<!--    --><?php //= $form->field($model, 'language')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
