<?php

namespace app\widgets;

use app\models\mock\MockCollections; // Используем новую модель
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * CollectionsListWidget рендерит список подборок.
 */
class CollectionsListWidget extends Widget
{
    /**
     * @var int Количество подборок на странице
     */
    public $pageSize = 10; // Можно сделать другое количество, если нужно

    /**
     * @var string Заголовок виджета (если понадобится)
     */
    public $title = 'Мои подборки';

    /**
     * @var bool Показывать ли заголовок
     */
    public $showTitle = false; // Обычно заголовок уже есть во вкладке

    /**
     * @var bool Показывать ли пагинацию
     */
    public $showPagination = true;

    /**
     * @var array Дополнительные HTML-атрибуты для контейнера
     */
    public $options = ['class' => 'collections-container'];

    /**
     * @var string Текст для пустого списка
     */
    public $emptyText = 'У вас пока нет созданных подборок';

    /**
     * @var array Дополнительные параметры для датапровайдера
     */
    public $dataProviderOptions = [];

    /**
     * @var array Параметры сортировки (по дате обновления, имени, кол-ву объектов)
     */
    public $sortAttributes = ['updatedAt', 'name', 'objectCount', 'clientName'];

    /**
     * @var ArrayDataProvider
     */
    private $_dataProvider;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->initDataProvider();
        // $this->registerClientFiles(); // Пока клиентские файлы не нужны
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('collections-list-view', [ // Используем новое представление
            'dataProvider' => $this->_dataProvider,
            'title' => $this->title,
            'showTitle' => $this->showTitle,
            'showPagination' => $this->showPagination,
            'options' => $this->options,
            'emptyText' => $this->emptyText,
        ]);
    }

    /**
     * Инициализирует датапровайдер для списка подборок
     */
    protected function initDataProvider()
    {
        $collections = MockCollections::findAll(); // Получаем данные из MockCollections

        $this->_dataProvider = new ArrayDataProvider(array_merge([
            'allModels' => $collections,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
            'sort' => [
                'attributes' => $this->sortAttributes,
                'defaultOrder' => [
                    'updatedAt' => SORT_DESC, // Сортировка по умолчанию
                ],
            ],
        ], $this->dataProviderOptions));
    }

    /**
     * Регистрирует необходимые файлы стилей и скриптов (если понадобятся)
     */
    // protected function registerClientFiles()
    // {
    //     $view = $this->getView();
    //     // $view->registerCssFile('@web/css/collections.css');
    //     // $view->registerJsFile('@web/js/collections.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    // }
}
