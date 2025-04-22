<?php

namespace app\widgets\Header;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class Header extends Widget
{
    // Основные настройки
    public $containerClass = 'container';
    public $logo = '/images/logo.png';
    public $logoWhite = '/images/logo-white.png';
    public $siteTitle = 'My Site';
    public $menu = [];
    
    // Опции для настройки
    public $options = [];
    public $stickyAreaOptions = [];
    public $containerOptions = [];
    public $navbarOptions = [];
    
    public function init()
    {
        parent::init();
        
        // Добавляем базовые классы
        Html::addCssClass($this->options, ['main-header', 'navbar-light', 'header-sticky', 'header-sticky-smart', 'header-mobile-lg']);
        Html::addCssClass($this->stickyAreaOptions, 'sticky-area');
        Html::addCssClass($this->navbarOptions, ['navbar', 'navbar-expand-lg', 'px-0']);
        Html::addCssClass($this->containerOptions, $this->containerClass);
        
        // Инициализируем меню если оно не передано
        if (empty($this->menu)) {
            $this->menu = [
              [
                'title' => 'About',
                'url' => '/about',
                'submenu' => [
                    [
                        'title' => 'About Us',
                        'url' => '/about',
                    ],
                    [
                        'title' => 'Contact Us',
                        'url' => '/contact',
                    ],
                ],
              ],
              [
                'title' => 'Contact',
                'url' => '/contact',
              ],
              [
                'title' => 'Blog',
                'url' => '/blog',
              ]
            ];
        }
    }

    public function run()
    {
        // Register required JavaScript
        $this->registerClientScript();
        
        return $this->render('_header', [
            'menu' => $this->menu,
            'logo' => $this->logo,
            'logoWhite' => $this->logoWhite,
            'siteTitle' => $this->siteTitle,
            'options' => $this->options,
            'stickyAreaOptions' => $this->stickyAreaOptions,
            'containerOptions' => $this->containerOptions,
            'navbarOptions' => $this->navbarOptions,
            'currentUrl' => Url::home(),
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
                console.log('Header widget initialized');
            ");
        }
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
}