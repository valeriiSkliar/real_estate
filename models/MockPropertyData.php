<?php

namespace app\models;

/**
 * Mock data for property listings
 */
class MockPropertyData
{
    /**
     * Get a list of mock property data
     * 
     * @param int $count Number of properties to generate
     * @return array Array of property data
     */
    public static function getProperties($count = 10)
    {
        $properties = [];
        
        for ($i = 0; $i < $count; $i++) {
            $properties[] = self::generateProperty($i);
        }
        
        return $properties;
    }
    
    /**
     * Generate a single property with mock data
     * 
     * @param int $id Property ID
     * @return array Property data
     */
    private static function generateProperty($id)
    {
        $districts = ['Центральный', 'Западный', 'Карасунский', 'Прикубанский'];
        $streets = ['Красная', 'Ставропольская', 'Кубанская', 'Московская', 'Северная'];
        $complexes = ['Новый город', 'Панорама', 'Губернский', 'Большой', 'Империал'];
        $statuses = ['Продажа', 'Аренда', 'Новостройка'];
        
        $district = $districts[array_rand($districts)];
        $street = $streets[array_rand($streets)];
        $complex = $complexes[array_rand($complexes)];
        $houseNumber = rand(1, 100);
        
        $price = rand(3, 15) * 1000000;
        $priceFormatted = number_format($price, 0, '.', ' ') . ' ₽';
        
        $squareMeters = rand(30, 150);
        $pricePerSquareMeter = round($price / $squareMeters);
        $pricePerSquareMeterFormatted = number_format($pricePerSquareMeter, 0, '.', ' ') . ' ₽/м²';
        
        return [
            'id' => $id + 1,
            'imageUrl' => '/images/properties/property-' . (($id % 5) + 1) . '.jpg',
            'price' => $priceFormatted,
            'priceRaw' => $price,
            'pricePerSquareMeter' => $pricePerSquareMeterFormatted,
            'status' => $statuses[array_rand($statuses)],
            'title' => 'ул. ' . $street . ', дом ' . $houseNumber,
            'address' => $district . ' район, ЖК ' . $complex . ', г. Краснодар',
            'bedrooms' => rand(1, 4),
            'bathrooms' => rand(1, 3),
            'squareFeet' => $squareMeters,
            'garages' => rand(0, 2),
            'imageCount' => rand(3, 12),
            'videoCount' => rand(0, 2),
            'detailUrl' => '/property/' . ($id + 1),
            'date' => date('Y-m-d', strtotime('-' . rand(1, 60) . ' days')),
            'complex' => $complex,
            'district' => $district
        ];
    }
}
