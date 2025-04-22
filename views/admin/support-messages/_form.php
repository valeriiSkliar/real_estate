<?php

use app\enums\SupportStatuses;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SupportMessages $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="support-messages-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->widget(Select2::class, [
        'data' => SupportStatuses::getAllCases(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
