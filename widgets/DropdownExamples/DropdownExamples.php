<?php

namespace app\widgets\DropdownExamples;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Widget for displaying Bootstrap dropdown menu examples
 */
class DropdownExamples extends Widget
{
    // Container options
    public $containerOptions = [];
    
    // Title for the examples section
    public $title = 'Bootstrap Dropdown Examples';
    
    // Examples to show (all by default)
    public $examples = [
        'basic',
        'split',
        'sizing',
        'directions',
        'menu-content',
        'forms'
    ];
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // Add default container class
        Html::addCssClass($this->containerOptions, 'dropdown-examples-container');
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Register required JavaScript
        $this->registerClientScript();
        
        return $this->render('index', [
            'containerOptions' => $this->containerOptions,
            'title' => $this->title,
            'examples' => $this->examples
        ]);
    }
    
    /**
     * Registers required JavaScript for dropdown functionality
     */
    protected function registerClientScript()
    {
        $view = $this->getView();
        
        // Register JavaScript file for dropdown examples
        if ($this->isDevServerRunning()) {
            // In development mode with webpack-dev-server
            $view->registerJsFile(
                "http://localhost:8090/assets/dist/js/main.js",
                ['depends' => [\app\assets\WebpackAsset::class]]
            );
        } else {
            // In production mode
            $view->registerJs("
                // Bootstrap dropdowns are already initialized by Bootstrap's JavaScript
                console.log('Dropdown examples initialized');
            ");
        }
    }
    
    /**
     * Gets the correct asset URL based on development or production environment
     * 
     * @param string $path The asset path
     * @return string The full asset URL
     */
    protected function getAssetUrl($path)
    {
        $isDevServer = $this->isDevServerRunning();
        
        if ($isDevServer) {
            return "http://localhost:8090/assets/dist/{$path}";
        }
        
        return "/assets/dist/{$path}";
    }
    
    /**
     * Checks if webpack-dev-server is running
     * 
     * @return bool
     */
    protected function isDevServerRunning()
    {
        try {
            $handle = @fsockopen('localhost', 8090);
            if ($handle) {
                fclose($handle);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
