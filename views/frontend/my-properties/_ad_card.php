<?php
/* @var $ad array */
/* @var $propertyTypeLabels array */
/* @var $tradeTypeLabels array */

use yii\helpers\Html;
use yii\helpers\Url;

$pricePerMeter = $ad['property_area'] > 0 ? number_format($ad['price'] / $ad['property_area'], 0, '.', ' ') : 0;

echo $this->render('/widgets/property-card-simple', [
    'price' => number_format($ad['price'], 0, '.', ' ') . ' ₽',
    'pricePerSquareMeter' => $pricePerMeter . ' ₽/м²',
    'address' => $ad['address'],
    'location' => $propertyTypeLabels[$ad['property_type']] . ', ' .
        $ad['property_area'] . ' м²' .
        (isset($ad['room_quantity']) && $ad['room_quantity'] > 0 ? ', ' . $ad['room_quantity'] . ' комн.' : ''),
    'detailUrl' => Url::to(['view', 'id' => $ad['id']]),
    'id' => $ad['id'],
]);
