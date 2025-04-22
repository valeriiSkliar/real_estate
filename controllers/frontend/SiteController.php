<?php

namespace app\controllers\frontend;

use Yii;
use yii\web\Response;

class SiteController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTest()
    {
        return $this->render('test');
    }
    
    /**
     * Returns district selector HTML for AJAX request
     * 
     * @return string
     */
    public function actionGetDistrictSelector()
    {
        $this->layout = false;
        return $this->render('_district_selector');
    }
    
    /**
     * Returns complex search form HTML for AJAX request
     * 
     * @return string
     */
    public function actionGetComplexSearch()
    {
        $this->layout = false;
        return $this->render('_complex_search');
    }
    
    /**
     * Searches for complexes based on query
     * 
     * @param string $query Search query
     * @return array
     */
    public function actionSearchComplexes($query)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Mock data for demonstration
        // In a real application, this would query the database
        $results = [];
        
        if (strlen($query) >= 3) {
            // Sample data - replace with actual database query
            $mockData = [
                ['id' => 'complex1', 'name' => 'ЖК Солнечный', 'address' => 'ул. Солнечная, 10'],
                ['id' => 'complex2', 'name' => 'ЖК Морской', 'address' => 'ул. Морская, 15'],
                ['id' => 'complex3', 'name' => 'ЖК Парковый', 'address' => 'ул. Парковая, 5'],
                ['id' => 'complex4', 'name' => 'ЖК Центральный', 'address' => 'ул. Центральная, 20'],
                ['id' => 'complex5', 'name' => 'ЖК Речной', 'address' => 'ул. Речная, 8'],
            ];
            
            // Filter mock data based on query
            foreach ($mockData as $complex) {
                if (stripos($complex['name'], $query) !== false || 
                    stripos($complex['address'], $query) !== false) {
                    $results[] = $complex;
                }
            }
        }
        
        return $results;
    }
}
