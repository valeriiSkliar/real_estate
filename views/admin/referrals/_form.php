<?php

use app\models\BotUsers;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Referrals $model */
/** @var yii\widgets\ActiveForm $form */
$userArray = ArrayHelper::map(BotUsers::find()->all(), 'id', 'uid');
?>

<div class="referrals-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->widget(Select2::class, [
        'data' =>  $userArray,
        'options' => ['placeholder' => 'Select a ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'referral_id')->widget(Select2::class, [
        'data' =>  $userArray,
        'options' => ['placeholder' => 'Select a ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
