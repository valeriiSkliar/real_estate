<?php

namespace app\widgets;

use app\models\mock\MockFavorites;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * FavoritesListWidget рендерит список избранных объявлений
 */
class FavoritesListWidget extends Widget
{
    /**
     * @var int ID пользователя
     */
    public $userId = 1;

    /**
     * @var int Количество объявлений на странице
     */
    public $pageSize = 9;

    /**
     * @var string Заголовок виджета
     */
    public $title = 'Избранное';

    /**
     * @var bool Показывать ли заголовок
     */
    public $showTitle = true;

    /**
     * @var bool Показывать ли пагинацию
     */
    public $showPagination = true;

    /**
     * @var string Вид отображения (grid, list)
     */
    public $viewType = 'grid';

    /**
     * @var array Дополнительные HTML-атрибуты для контейнера
     */
    public $options = ['class' => 'favorites-container'];

    /**
     * @var string Текст для пустого списка
     */
    public $emptyText = 'У вас пока нет избранных объявлений';

    /**
     * @var array Дополнительные параметры для датапровайдера
     */
    public $dataProviderOptions = [];

    /**
     * @var array Параметры сортировки
     */
    public $sortAttributes = ['id', 'price', 'title'];

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

        // Регистрируем необходимый CSS и JavaScript
        $this->registerClientFiles();

        // Получаем данные и создаем датапровайдер
        $this->initDataProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Рендерим представление
        return $this->render('favorites-list-view', [
            'dataProvider' => $this->_dataProvider,
            'title' => $this->title,
            'showTitle' => $this->showTitle,
            'showPagination' => $this->showPagination,
            'viewType' => $this->viewType,
            'options' => $this->options,
            'emptyText' => $this->emptyText,
        ]);
    }

    /**
     * Инициализирует датапровайдер для списка избранного
     */
    protected function initDataProvider()
    {
        // Получаем данные из модели
        $favorites = MockFavorites::findAll($this->userId);

        // Создаем датапровайдер для пагинации
        $this->_dataProvider = new ArrayDataProvider(array_merge([
            'allModels' => $favorites,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
            'sort' => [
                'attributes' => $this->sortAttributes,
            ],
        ], $this->dataProviderOptions));
    }

    /**
     * Регистрирует необходимые файлы стилей и скриптов
     */
    protected function registerClientFiles()
    {
        $view = $this->getView();

        // Регистрируем CSS
        // $view->registerCssFile('@web/css/favorites.css');

        // Регистрируем JS
        $view->registerJsFile('@web/js/components/favorite-button.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    }
}
