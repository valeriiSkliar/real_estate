<?php

use app\enums\PaymentStatuses;
use app\models\Languages;
use app\models\Tariffs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payments-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'status')->widget(Select2::class, [
        'data' => PaymentStatuses::getPaymentStatuses(),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <?= $form->field($model, 'tariff_id')->widget(Select2::class, [
        'data' => ArrayHelper::map(Tariffs::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
