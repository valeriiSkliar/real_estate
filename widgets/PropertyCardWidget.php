<?php

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * PropertyCardWidget displays a property card with image, details, and interactive elements.
 *
 * Usage:
 * <?= PropertyCardWidget::widget([
 *     'property' => $propertyModel,
 *     'imageUrl' => 'images/property.jpg',
 *     'price' => '$1,250,000',
 *     'status' => 'For Sale',
 *     'title' => 'Home in Metric Way',
 *     'address' => '1421 San Pedro St, Los Angeles',
 *     'bedrooms' => 3,
 *     'bathrooms' => 3,
 *     'squareFeet' => 2300,
 *     'garages' => 1,
 *     'imageCount' => 9,
 *     'videoCount' => 2,
 *     'detailUrl' => 'single-property-1.html',
 * ]) ?>
 */
class PropertyCardWidget extends Widget
{
    /**
     * @var object|null The property model (optional)
     */
    public $property = null;
    
    /**
     * @var string The URL of the property image
     */
    public $imageUrl = '';
    
    /**
     * @var string The property price
     */
    public $price = '';
    
    /**
     * @var string The property status (For Sale, For Rent, etc.)
     */
    public $status = '';
    
    /**
     * @var string The property title
     */
    public $title = '';
    
    /**
     * @var string The property address
     */
    public $address = '';
    
    /**
     * @var int Number of bedrooms
     */
    public $bedrooms = 0;
    
    /**
     * @var int Number of bathrooms
     */
    public $bathrooms = 0;
    
    /**
     * @var int Square footage
     */
    public $squareFeet = 0;
    
    /**
     * @var int Number of garages
     */
    public $garages = 0;
    
    /**
     * @var int Number of images
     */
    public $imageCount = 0;
    
    /**
     * @var int Number of videos
     */
    public $videoCount = 0;
    
    /**
     * @var string URL to the property detail page
     */
    public $detailUrl = '#';
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // If a property model is provided, extract the values from it
        if ($this->property !== null) {
            // Map property model attributes to widget properties
            // This is just an example, adjust according to your actual model structure
            if (empty($this->price) && isset($this->property->price)) {
                $this->price = $this->property->price;
            }
            
            if (empty($this->title) && isset($this->property->title)) {
                $this->title = $this->property->title;
            }
            
            if (empty($this->address) && isset($this->property->address)) {
                $this->address = $this->property->address;
            }
            
            // Add other mappings as needed
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('@app/views/widgets/property-card', [
            'imageUrl' => $this->imageUrl,
            'price' => $this->price,
            'status' => $this->status,
            'title' => $this->title,
            'address' => $this->address,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'squareFeet' => $this->squareFeet,
            'garages' => $this->garages,
            'imageCount' => $this->imageCount,
            'videoCount' => $this->videoCount,
            'detailUrl' => $this->detailUrl,
        ]);
    }
}
