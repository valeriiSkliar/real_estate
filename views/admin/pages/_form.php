<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Pages $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="page-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'] // Не забудьте добавить enctype для загрузки файлов
    ]); ?>

    <?= $form->field($model, 'command')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'h1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->widget(\dominus77\tinymce\TinyMce::class, [
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
            'toolbar1' => 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontsizeselect',
            'toolbar2' => 'print preview media | forecolor backcolor emoticons | codesample | typograf',
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
                    formData.append('entity', 'pages');
                    
                    xhr.send(formData);
                }
            "),
        ]
    ]) ?>
    <div class="form-group my-3">
        <?php if ($model->image): ?>
            <!-- Отображение текущего изображения -->
            <?= Html::a(
            Html::img($model->image,
                ['class' => 'img-thumbnail', 'target' => '_blank', 'width' => 250]),
            $model->image) ?>

            <div>
                <?= Html::a('Удалить картинку', ['delete-media', 'type' => 'image', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-sm my-2',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить картинку?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>

        <?php endif; ?>
        <?= $form->field($model, 'imageFile')->fileInput() ?>

    </div>
    <!-- Отображение текущего файла -->
    <?php if ($model->file): ?>
        <div class="form-group my-3">
            <?= Html::label('Текущий файл') ?>
            <?= Html::a('Скачать файл', $model->file, ['target' => '_blank']) ?>
            <div class="my-1">
                <?= Html::a('Удалить файл', ['delete-media', 'type' => 'file', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-sm ml-2',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить файл?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Поле для загрузки нового файла -->
    <?= $form->field($model, 'fileUpload')->fileInput()->label('Загрузить новый файл') ?>

    <!-- Отображение текущего видео -->
    <?php if ($model->video): ?>
        <div class="form-group my-3">
            <?= Html::label('Текущее видео') ?>
            <div>
                <video width="320" height="240" controls>
                    <source src="<?= Url::to($model->video) ?>" type="video/mp4">
                    Ваш браузер не поддерживает видео тег.
                </video>
                <div>
                <?= Html::a('Удалить видео', ['delete-media', 'type' => 'video', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-sm ml-2',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить видео?',
                        'method' => 'post',
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div><b style="color: red">Видео должно быть квадратное, для правильного отображения в телеграм</b></div>
    <?php endif; ?>
    <!-- Поле для загрузки нового видео -->
    <?= $form->field($model, 'videoFile')->fileInput(['accept' => 'video/*'])->label('Загрузить новое видео') ?>

    <!-- Отображение текущего аудио -->
    <?php if ($model->audio): ?>
        <div class="form-group my-3">
            <?= Html::label('Текущее аудио') ?>
            <div>
                <audio controls>
                    <source src="<?= Url::to($model->audio) ?>" type="audio/mpeg">
                    Ваш браузер не поддерживает аудио тег.
                </audio>
                <div>
                <?= Html::a('Удалить аудио', ['delete-media', 'type' => 'audio', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-sm ml-2',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить аудио?',
                        'method' => 'post',
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Поле для загрузки нового аудио -->
    <?= $form->field($model, 'audioFile')->fileInput(['accept' => 'audio/*'])->label('Загрузить новое аудио') ?>

    <div class="my-3">
        <h3 class="my-2">Мета данные:</h3>
        <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="form-group my-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
