<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UserInfo $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-info-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php if ($model->photo_url): ?>
        <div class="my-3">
            <img src="<?= trim(Yii::$app->params['url'], '/') . $model->photo_url ?>" width="250" class="img-thumbnail" />
        </div>
    <?php endif;?>
    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'description')->widget(\dominus77\tinymce\TinyMce::class, [
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
            'images_upload_handler' => new \yii\web\JsExpression("
            function (blobInfo, success, failure) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/image-tiny-uploader/upload');
                
                xhr.onload = function() {
                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    
                    var json = JSON.parse(xhr.responseText);
                    
                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    
                    success(json.location);
                };
                
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('entity', 'user-info');
                
                xhr.send(formData);
            }
        "),
        ]
    ]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'telegram')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'language')->widget(kartik\select2\Select2::class, [
        'data' => yii\helpers\ArrayHelper::map(\app\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
        'options' => [
            'prompt'  => 'Выберите...'
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
