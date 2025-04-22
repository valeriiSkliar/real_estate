<?php

use app\enums\SendsDestinationTypes;
use app\enums\SendsProviderTypes;
use app\models\Languages;
use app\models\Sends;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */
/* @var $form yii\widgets\ActiveForm */

$model->destinationArray = explode(',', $model->destination);
?>
<style>
    .custom-checkbox-item {
        margin-left: 5px;
    }
</style>
<div class="sends-form">
    <h1> Время сервера: <span style="color: red"><?= date('Y-m-d H:i:s') ?></span></h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату и время ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss'
        ]
    ]) ?>

<!--    --><?php //= $form->field($model, 'is_regular')->checkbox() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

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
            'images_upload_handler' => new JsExpression("
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
                formData.append('entity', 'sends');
                
                xhr.send(formData);
            }
        "),
        ]
    ]) ?>

    <div class="form-group my-3" style="background-color: white">
        <?php if ($model->image_url): ?>
            <!-- Отображение текущего изображения -->
            <?= Html::a(
                Html::img($model->image_url,
                    ['class' => 'img-thumbnail', 'target' => '_blank', 'width' => 250]),
                $model->image_url) ?>

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
        <?= $form->field($model, 'imageFile')->fileInput()->label('Загрузить новое изображение') ?>

    </div>
    <div style="background-color: gainsboro">
    <!-- Отображение текущего файла -->
    <?php if ($model->file_url): ?>
        <div class="form-group my-3">
            <?= Html::label('Текущий файл') ?>
            <?= Html::a('Скачать файл', $model->file_url, ['target' => '_blank']) ?>
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
    </div>
    <div style="background-color: white">
    <!-- Отображение текущего видео -->
    <?php if ($model->video_url): ?>
        <div class="form-group my-3">
            <?= Html::label('Текущее видео') ?>
            <div>
                <video width="320" height="240" controls>
                    <source src="<?= Url::to($model->video_url) ?>" type="video/mp4">
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
    </div>
    <div style="background-color: gainsboro">
    <!-- Отображение текущего аудио -->
    <?php if ($model->audio_url): ?>
        <div class="form-group my-3">
            <?= Html::label('Текущее аудио') ?>
            <div>
                <audio controls>
                    <source src="<?= Url::to($model->audio_url) ?>" type="audio/mpeg">
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
    </div>
    <?= $form->field($model, 'destinationArray')->checkboxList(SendsDestinationTypes::getAllCases(), [
        'itemOptions' => [
            'class' => 'custom-checkbox-item'
        ]
    ]) ?>

<!--    --><?php //= $form->field($model, 'provider')->dropDownList(SendsProviderTypes::getAllCases()) ?>
    <?= $form->field($model, 'provider')->hiddenInput(['value' => SendsProviderTypes::TELEGRAM->value])->label(false) ?>
<!--    --><?php //= $form->field($model, 'language')->widget(Select2::class, [
//        'data' => ArrayHelper::map(Languages::find()->orderBy(['name' => SORT_ASC])->all(), 'slug', 'name'),
//        'options' => [
//            'prompt'  => 'Выберите...'
//        ]
//    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(Sends::$STATUSES) ?>

    <div style="background-color: gainsboro">
        <div><b>Для единичной рассылки пользователю поставьте галочку и выберите пользователя: </b></div>
        <br>
        <?= $form->field($model, 'is_single')->checkbox() ?>

        <?= $form->field($model, 'recipient')->widget(Select2::class, [
            'options' => ['placeholder' => 'Выберите или введите (ID,telegram id или почту...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 2,
                'ajax' => [
                    'url' => '/admin/sends/find-user',
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'processResults' => new JsExpression('function(data) {
                return {results: data.results};
            }'),
                ],
            ],
        ]) ?>
        <br>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
