<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Texts $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="texts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->widget(\dominus77\tinymce\TinyMce::class, [
        'options' => [
            'rows' => 20,
            'placeholder' => true,
        ],
        'language' => 'ru',
        'clientOptions' => [
            'menubar' => true,
            'statusbar' => true,
            'plugins' => [
                'typograf advlist autolink lists link image charmap print preview hr anchor pagebreak placeholder',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc noneditable',
            ],
            'contextmenu' => 'typograf | link image inserttable | cell row column deletetable',
            'noneditable_noneditable_class' => 'fa',
            'extended_valid_elements' => 'span[class|style]',
            'toolbar1' => 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontsizeselect','toolbar2' => 'print preview media | forecolor backcolor emoticons | codesample | typograf',
        ]
    ]) ?>

<!--    --><?php //= $form->field($model, 'language')->widget(kartik\select2\Select2::class, [
//        'data' => yii\helpers\ArrayHelper::map(\app\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
//        'options' => [
//            'prompt'  => 'Выберите...'
//        ]
//    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
