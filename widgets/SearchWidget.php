<?php

namespace app\widgets;

use yii\base\Widget;

/**
 * SearchWidget provides a customizable search form for the real estate application.
 *
 * Usage:
 * <?= SearchWidget::widget([
 *     'action' => ['site/search'],
 *     'method' => 'get',
 *     'options' => ['class' => 'search-form'],
 *     'placeholder' => 'Search properties...',
 *     'buttonText' => 'Search',
 *     'advancedSearch' => true,
 *     'categories' => [
 *         'all' => 'All Categories',
 *         'rent' => 'For Rent',
 *         'sale' => 'For Sale',
 *     ],
 *     'selectedCategory' => 'all',
 * ]) ?>
 */
class SearchWidget extends Widget
{
    /**
     * @var array|string The URL for the search form action
     */
    public $action = ['/'];
    
    /**
     * @var string The form method (get or post)
     */
    public $method = 'get';
    
    /**
     * @var array HTML options for the form
     */
    public $options = ['class' => 'search-form'];
    
    /**
     * @var string Placeholder text for the search input
     */
    public $placeholder = 'Search properties...';
    
    /**
     * @var string Text for the search button
     */
    public $buttonText = 'Search';
    
    /**
     * @var string CSS class for the search button
     */
    public $buttonClass = 'btn btn-primary';
    
    /**
     * @var string Icon class for the search button (Font Awesome)
     */
    public $buttonIcon = 'fas fa-search';
    
    /**
     * @var bool Whether to show the search button icon
     */
    public $showButtonIcon = true;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('@app/views/widgets/search', [
            'action' => $this->action,
            'method' => $this->method,
            'options' => $this->options,
            'placeholder' => $this->placeholder,
            'buttonText' => $this->buttonText,
            'buttonClass' => $this->buttonClass,
            'buttonIcon' => $this->buttonIcon,
            'showButtonIcon' => $this->showButtonIcon,
        ]);
    }
}
