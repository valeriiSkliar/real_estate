<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Cities;

/* @var $this yii\web\View */
/* @var $model app\models\Advertisements */
/* @var $existingImages app\models\AdvertisementImages[] */
/* @var $cities array */
/* @var $propertyTypes array */
/* @var $tradeTypes array */
/* @var $conditions array */

$this->title = 'Редактирование объявления';
$this->params['breadcrumbs'][] = ['label' => 'Объявления', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="mobile-advertisement-update">
    <div class="mobile-header sticky-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-2">
                <?= Html::a('<i class="fas fa-arrow-left"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-link text-dark p-0']) ?>
                <h1 class="h5 mb-0"><?= Html::encode($this->title) ?></h1>
                <div style="width: 24px;"></div> <!-- Placeholder for alignment -->
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <?php $form = ActiveForm::begin([
            'id' => 'advertisement-form',
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'inputOptions' => ['class' => 'form-control'],
                'errorOptions' => ['class' => 'invalid-feedback d-block'],
            ],
        ]); ?>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Основная информация</h5>
            </div>
            <div class="card-body">
                <?= $form->field($model, 'property_type')->dropDownList($propertyTypes, [
                    'prompt' => 'Выберите тип недвижимости',
                ]) ?>

                <?= $form->field($model, 'trade_type')->dropDownList($tradeTypes, [
                    'prompt' => 'Выберите тип сделки',
                ]) ?>

                <?= $form->field($model, 'city_id')->dropDownList(
                    ArrayHelper::map($cities, 'id', 'name'),
                    ['prompt' => 'Выберите город']
                ) ?>

                <?= $form->field($model, 'district')->textInput(['maxlength' => true, 'placeholder' => 'Введите район']) ?>

                <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => 'Введите адрес']) ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Характеристики</h5>
            </div>
            <div class="card-body">
                <?= $form->field($model, 'room_quantity')->input('number', [
                    'min' => 0,
                    'placeholder' => 'Введите количество комнат'
                ]) ?>

                <?= $form->field($model, 'property_area')->input('number', [
                    'min' => 0,
                    'placeholder' => 'Введите площадь недвижимости (м²)',
                    'step' => '0.01'
                ]) ?>

                <?= $form->field($model, 'land_area')->input('number', [
                    'min' => 0,
                    'placeholder' => 'Введите площадь участка (м²)',
                    'step' => '0.01'
                ]) ?>

                <?= $form->field($model, 'condition')->dropDownList($conditions, [
                    'prompt' => 'Выберите состояние',
                ]) ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Цена и контакты</h5>
            </div>
            <div class="card-body">
                <?= $form->field($model, 'price')->input('number', [
                    'min' => 0,
                    'placeholder' => 'Введите цену'
                ]) ?>

                <?= $form->field($model, 'realtor_phone')->textInput([
                    'placeholder' => '+7XXXXXXXXXX',
                    'type' => 'tel',
                    'pattern' => '[+][0-9]{11,12}',
                ]) ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Описание</h5>
            </div>
            <div class="card-body">
                <?= $form->field($model, 'clean_description')->textarea([
                    'rows' => 4,
                    'placeholder' => 'Введите подготовленное описание'
                ]) ?>

                <?= $form->field($model, 'raw_description')->textarea([
                    'rows' => 4,
                    'placeholder' => 'Введите исходное описание'
                ]) ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Фотографии</h5>
            </div>
            <div class="card-body">
                <!-- Existing Images -->
                <?php if (!empty($existingImages)): ?>
                    <h6 class="mb-3">Текущие фотографии</h6>
                    <div class="row" id="existingImagesContainer">
                        <?php foreach ($existingImages as $index => $image): ?>
                            <div class="col-4 mb-3 existing-image-item" data-id="<?= $image->id ?>">
                                <div class="image-preview-container">
                                    <img src="<?= Yii::getAlias('@web/uploads/advertisements/' . $image->image) ?>" class="image-preview">
                                    <div class="image-delete-btn" onclick="deleteExistingImage(<?= $image->id ?>)">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                <?php endif; ?>

                <!-- Upload New Images -->
                <div class="form-group">
                    <label class="control-label">Загрузите новые фотографии</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="advertisementImages" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                        <label class="btn btn-outline-secondary w-100 mt-2" for="advertisementImages">
                            <i class="fas fa-camera me-2"></i>Выбрать фотографии
                        </label>
                    </div>
                </div>

                <div class="row mt-3" id="imagePreviewContainer">
                    <!-- Image previews will be shown here -->
                </div>
            </div>
        </div>

        <div class="form-group mb-5 pb-5">
            <div class="d-grid gap-2">
                <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success btn-lg']) ?>
                <?= Html::a('Отмена', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs("
    function previewImages(input) {
        const container = document.getElementById('imagePreviewContainer');
        
        if (input.files) {
            // Calculate remaining slots for images (max 10 total)
            const existingImagesCount = document.querySelectorAll('.existing-image-item').length;
            const maxNewImages = 10 - existingImagesCount;
            
            // Clear container if too many files
            if (input.files.length > maxNewImages) {
                alert('Можно загрузить максимум ' + maxNewImages + ' новых фотографий');
                input.value = '';
                return;
            }
            
            container.innerHTML = '';
            
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                
                // Check if file is an image
                if (!file.type.match('image.*')) {
                    continue;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 mb-3';
                    
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview-container';
                    
                    const img = document.createElement('img');
                    img.className = 'image-preview';
                    img.src = e.target.result;
                    
                    const deleteBtn = document.createElement('div');
                    deleteBtn.className = 'image-delete-btn';
                    deleteBtn.innerHTML = '<i class=\"fas fa-times\"></i>';
                    deleteBtn.onclick = function() {
                        col.remove();
                    };
                    
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(deleteBtn);
                    col.appendChild(previewContainer);
                    container.appendChild(col);
                };
                
                reader.readAsDataURL(file);
            }
        }
    }
    
    function deleteExistingImage(imageId) {
        if (confirm('Вы уверены, что хотите удалить это изображение?')) {
            $.ajax({
                url: '" . \yii\helpers\Url::to(['delete-image']) . "',
                type: 'POST',
                data: {
                    imageId: imageId,
                    _csrf: $('meta[name=\"csrf-token\"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('.existing-image-item[data-id=\"' + imageId + '\"]').remove();
                    } else {
                        alert('Ошибка при удалении изображения');
                    }
                },
                error: function() {
                    alert('Ошибка при удалении изображения');
                }
            });
        }
    }
", \yii\web\View::POS_HEAD);
?>

<!-- Fixed bottom action bar for save/cancel -->
<div class="fixed-bottom bg-white border-top p-3 action-bar">
    <div class="container">
        <div class="row g-2">
            <div class="col-6">
                <a href="<?= \yii\helpers\Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-outline-secondary w-100">Отмена</a>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-success w-100" onclick="$('#advertisement-form').submit()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<style>
    .mobile-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
        z-index: 1030;
    }

    .action-bar {
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1020;
    }

    /* Space at the bottom to prevent content from being hidden behind the action bar */
    .mb-5.pb-5 {
        margin-bottom: 5rem !important;
    }

    /* Image preview styles */
    .image-preview-container {
        position: relative;
        width: 100%;
        padding-bottom: 100%;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .image-preview {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
    }