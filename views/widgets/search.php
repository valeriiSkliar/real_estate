<?php
/**
 * @var string|array $action
 * @var string $method
 * @var array $options
 * @var string $placeholder
 * @var string $buttonText
 * @var array $categories
 * @var string $selectedCategory
 * @var array $priceRanges
 * @var string $selectedPriceRange
 * @var array $locations
 * @var string $selectedLocation
 * @var string $buttonClass
 * @var string $buttonIcon
 * @var bool $showButtonIcon
 */

use yii\helpers\Html;
?>

<div class="property-search-form container mt-2">
    <?= Html::beginForm($action, $method, $options + ['id' => 'main-search-form', 'class' => 'search-form']) ?>
    
    <div class="search-main-wrapper col-md-8">
        <div class="row">
            <!-- Search input -->
            <div class="col-10 p-0">
                <div class="input-group">                    
                    <?= Html::textInput('q', isset($_GET['q']) ? $_GET['q'] : null, [
                        'class' => 'form-control border-0 shadow-none',
                        'placeholder' => $placeholder,
                        'aria-label' => $placeholder,
                    ]) ?>
                </div>
            </div>

            <!-- Search button -->
            <div class="col-2 p-0">
                <?= Html::submitButton(
                    ($showButtonIcon ? '<i class="' . Html::encode($buttonIcon) . ' me-1"></i> ' : ''),
                    [
                        'class' => $buttonClass . ' w-100',
                    ]
                ) ?>
            </div>
        </div>
    </div>
    
    <?= Html::endForm() ?>
</div>
