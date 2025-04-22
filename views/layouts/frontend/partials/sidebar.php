<?php
/**
 * Mobile sidebar component
 */
use yii\helpers\Html;
?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="mobile-sidebar" id="mobileSidebar">
    <div class="sidebar-header">
        <button type="button" class="btn-close" id="closeSidebar" aria-label="Close">
            <i class="fa fa-arrow-left"></i>
        </button>
        <div class="sidebar-title">
            Фильтры
        </div>
        <button type="button" class="btn-clear" id="resetForm" aria-label="Reset">
            Очистить
        </button>
    </div>
    
    <div class="sidebar-body">
        <!-- Property Filters -->
        <div class="sidebar-filters">
            <?= Html::beginForm(['/'], 'get', ['class' => 'sidebar-filter-form', 'id' => 'sidebarFilterForm']) ?>
                
                <!-- Тип сделки -->
                <div class="deal-type-tabs">
                    <div class="tab-group">
                        <div class="tab-item active">
                            <?= Html::radio('deal_type', true, [
                                'value' => 'buy',
                                'id' => 'deal-type-buy',
                                'class' => 'tab-input',
                            ]) ?>
                            <?= Html::label('Купить', 'deal-type-buy', ['class' => 'tab-label mb-0']) ?>
                        </div>
                        <div class="tab-item">
                            <?= Html::radio('deal_type', false, [
                                'value' => 'rent',
                                'id' => 'deal-type-rent',
                                'class' => 'tab-input',
                            ]) ?>
                            <?= Html::label('Снять', 'deal-type-rent', ['class' => 'tab-label mb-0']) ?>
                        </div>
                    </div>
                </div>
                
                <!-- Срок аренды -->
                <div class="rent-period-tabs">
                    <div class="tab-group">
                        <div class="tab-item active">
                            <?= Html::radio('rent_period', true, [
                                'value' => 'long',
                                'id' => 'rent-period-long',
                                'class' => 'tab-input',
                            ]) ?>
                            <?= Html::label('Надолго', 'rent-period-long', ['class' => 'tab-label mb-0']) ?>
                        </div>
                        <div class="tab-item">
                            <?= Html::radio('rent_period', false, [
                                'value' => 'daily',
                                'id' => 'rent-period-daily',
                                'class' => 'tab-input',
                            ]) ?>
                            <?= Html::label('Посуточно', 'rent-period-daily', ['class' => 'tab-label mb-0']) ?>
                        </div>
                    </div>
                </div>
                
                <!-- Тип недвижимости -->
                <div class="form-group">
                    <?= Html::dropDownList('property_type', null, [
                        'apartment' => 'Квартиру',
                    ], ['class' => 'form-control']) ?>
                    <i class="fa fa-chevron-down dropdown-icon"></i>
                </div>
                
                <!-- Цена -->
                <div class="filter-section">
                    <div class="filter-title">Цена</div>
                    <div class="price-range">
                        <div class="price-input">
                            <?= Html::textInput('price_min', null, [
                                'class' => 'form-control',
                                'placeholder' => 'от 35 000',
                            ]) ?>
                            <span class="currency-symbol">₽</span>
                        </div>
                        <div class="price-input">
                            <?= Html::textInput('price_max', null, [
                                'class' => 'form-control',
                                'placeholder' => 'до 300 000',
                            ]) ?>
                            <span class="currency-symbol">₽</span>
                        </div>
                    </div>
                </div>
                
                <!-- Площадь -->
                <div class="filter-section">
                    <div class="filter-title">Общая площадь</div>
                    <div class="area-range">
                        <div class="area-input">
                            <?= Html::textInput('area_min', null, [
                                'class' => 'form-control',
                                'placeholder' => 'от',
                            ]) ?>
                            <span class="area-symbol">м²</span>
                        </div>
                        <div class="area-input">
                            <?= Html::textInput('area_max', null, [
                                'class' => 'form-control',
                                'placeholder' => 'до',
                            ]) ?>
                            <span class="area-symbol">м²</span>
                        </div>
                    </div>
                </div>
                
                <!-- Расположение -->
                <div class="filter-section">
                    <div class="filter-title">Расположение</div>
                    <div class="location-option" data-option-type="district">
                        <i class="fa fa-map-marker-alt location-icon"></i>
                        <div class="location-text">Район</div>
                        <i class="fa fa-chevron-right arrow-icon"></i>
                    </div>
                    <div class="location-option" data-option-type="complex">
                        <i class="fa fa-search location-icon"></i>
                        <div class="location-text">Название ЖК, адрес, район, ж/д...</div>
                        <i class="fa fa-chevron-right arrow-icon"></i>
                    </div>
                </div>
                
                <!-- Submit button -->
                <div class="search-button-container">
                    <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary btn-search w-100']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
