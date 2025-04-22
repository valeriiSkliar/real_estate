<?php
/**
 * Example of how to use the PropertyCardWidget
 */

use app\widgets\PropertyCardWidget;

// Example 1: Basic usage with individual properties
echo PropertyCardWidget::widget([
    'imageUrl' => '/images/properties-grid-17.jpg',
    'price' => '$1,250,000',
    'status' => 'For Sale',
    'title' => 'Home in Metric Way',
    'address' => '1421 San Pedro St, Los Angeles',
    'bedrooms' => 3,
    'bathrooms' => 3,
    'squareFeet' => 2300,
    'garages' => 1,
    'imageCount' => 9,
    'videoCount' => 2,
    'detailUrl' => '/property/view?id=123',
]);

// Example 2: Using with a property model
// Assuming $model is your property model with appropriate attributes
/*
echo PropertyCardWidget::widget([
    'property' => $model,
    'imageUrl' => $model->getMainImageUrl(),
    'detailUrl' => ['/property/view', 'id' => $model->id],
]);
*/

// Example 3: Multiple property cards in a grid
/*
<div class="row">
    <?php foreach ($properties as $property): ?>
        <div class="col-md-4 mb-4">
            <?= PropertyCardWidget::widget([
                'property' => $property,
                'imageUrl' => $property->getMainImageUrl(),
                'price' => Yii::$app->formatter->asCurrency($property->price),
                'status' => $property->status,
                'title' => $property->title,
                'address' => $property->address,
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'squareFeet' => $property->square_feet,
                'garages' => $property->garages,
                'imageCount' => count($property->images),
                'videoCount' => count($property->videos),
                'detailUrl' => ['/property/view', 'id' => $property->id],
            ]) ?>
        </div>
    <?php endforeach; ?>
</div>
*/
?>
