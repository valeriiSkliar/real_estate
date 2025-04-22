<?php

use app\enums\ActiveStatuses;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SourceChats $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="source-chats-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'chat_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'platform')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active')->widget(Select2::class, [
        'data' => ActiveStatuses::getAllCases(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <?= $form->field($model, 'stop_words')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
