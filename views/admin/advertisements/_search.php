<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\AdvertisementsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="advertisements-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'property_type') ?>

    <?= $form->field($model, 'trade_type') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'realtor_phone') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'clean_description') ?>

    <?php // echo $form->field($model, 'raw_description') ?>

    <?php // echo $form->field($model, 'property_name') ?>

    <?php // echo $form->field($model, 'locality') ?>

    <?php // echo $form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'room_quantity') ?>

    <?php // echo $form->field($model, 'property_area') ?>

    <?php // echo $form->field($model, 'land_area') ?>

    <?php // echo $form->field($model, 'condition') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
