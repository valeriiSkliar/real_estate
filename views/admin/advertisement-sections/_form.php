<?php

use app\enums\PropertyTypes;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AdvertisementSections $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="advertisement-sections-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'type')->widget(Select2::class, [
        'data' => PropertyTypes::getAllCases(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
