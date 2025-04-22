<?php

use app\models\Buttons;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Buttons $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="buttons-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
<!--    --><?php //= $form->field($model, 'language')->widget(kartik\select2\Select2::class, [
//        'data' => yii\helpers\ArrayHelper::map(\app\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
//        'options' => [
//            'prompt'  => 'Выберите...'
//        ]
//    ]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'web_app_link')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'is_hidden')->dropDownList(Buttons::VISIBILITY) ?>
<!--    --><?php //= $form->field($model, 'type')->dropDownList(Buttons::TYPES) ?>
    <?= $form->field($model, 'type')->hiddenInput(['value' => Buttons::TYPE_INLINE])->label(false) ?>
    <?= $form->field($model, 'position')->dropDownList(Buttons::POSITIONS) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
