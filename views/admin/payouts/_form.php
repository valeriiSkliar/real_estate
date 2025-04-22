<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Payouts $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payouts-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if ($model->isNewRecord): ?>

        <?= $form->field($model, 'uid')->widget(Select2::classname(), [
            'data'          => ArrayHelper::map(\app\models\BotUsers::find()->all(), 'id', function ($model) {
                return $model->uid . ' - Bonus: ' . $model->bonus;
            }),
            'options'       => ['placeholder' => 'Select a ...'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); ?>


        <?= $form->field($model, 'amount')->textInput() ?>

    <?php else: ?>

        <?= $form->field($model, 'status')->widget(Select2::classname(), [
            'data'          => \app\models\Payouts::statusList(),
            'options'       => ['placeholder' => 'Select a ...'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); ?>

    <?php endif ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
