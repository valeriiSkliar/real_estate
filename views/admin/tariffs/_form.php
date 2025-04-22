<?php

use app\enums\PaymentProviders;
use app\enums\Tariff;
use app\enums\ActiveStatuses;
use app\models\Languages;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Tariffs $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tariffs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

<!--    --><?php //= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<!--    --><?php //= $form->field($model, 'payment_description')->textarea(['rows' => 6]) ?>

<!--    --><?php //= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currency_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>

<!--    --><?php //= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

<!--    --><?php //= $form->field($model, 'language')->widget(Select2::class, [
//        'data' => ArrayHelper::map(Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
//        'options' => [
//            'prompt'  => 'Выберите...'
//        ]
//    ]) ?>

    <?= $form->field($model, 'type')->widget(Select2::class, [
        'data' => Tariff::getTariffs(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

        <?= $form->field($model, 'provider')->widget(Select2::class, [
            'data' => PaymentProviders::getAllCases(),
            'options' => [
                'prompt'  => 'Выберите...'
            ]
        ]) ?>

<!--    --><?php //= $form->field($model, 'bank_provider')->textInput(['maxlength' => true]) ?>

<!--    --><?php //= $form->field($model, 'uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subscription_id')->textInput(['maxlength' => true]) ?>

<!--    --><?php //= $form->field($model, 'status')->widget(Select2::class, [
//        'data' => TariffStatuses::getAllCases(),
//        'options' => [
//            'prompt'  => 'Выберите...'
//        ]
//    ]) ?>

<!--    <div class="my-3"><b>Показать на главной странице оплаты: </b>--><?php //= Html::activeCheckbox($model, 'is_main', ['label' => false]) ?><!--</div>-->

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
