<?php

use app\enums\PropertyTypes;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Advertisements $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="advertisements-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'property_type')->widget(Select2::class, [
        'data' => PropertyTypes::getAllCases(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <?= $form->field($model, 'trade_type')->widget(Select2::class, [
        'data' => \app\enums\TradeTypes::getAllCases(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <?= $form->field($model, 'source')->widget(Select2::class, [
        'data' => \app\enums\Source::getAllCases(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>


    <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'realtor_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'clean_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'raw_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'property_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'locality')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'district')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'room_quantity')->textInput() ?>

    <?= $form->field($model, 'property_area')->textInput() ?>

    <?= $form->field($model, 'land_area')->textInput() ?>

    <?= $form->field($model, 'condition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
