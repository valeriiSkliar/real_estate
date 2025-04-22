<?php

namespace app\widgets;

use app\models\mock\MockFavorites;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * FavoriteButtonWidget рендерит кнопку добавления/удаления из избранного
 */
class FavoriteButtonWidget extends Widget
{
    /**
     * @var int ID объявления
     */
    public $propertyId;

    /**
     * @var bool Находится ли объявление в избранном
     */
    public $isFavorite = false;

    /**
     * @var string Класс для кнопки
     */
    public $buttonClass = 'btn-icon favorite-btn';

    /**
     * @var string Заголовок для кнопки добавления в избранное
     */
    public $addTitle = 'Добавить в избранное';

    /**
     * @var string Заголовок для кнопки удаления из избранного
     */
    public $removeTitle = 'Удалить из избранного';

    /**
     * @var array Дополнительные HTML-атрибуты для кнопки
     */
    public $options = [];

    /**
     * @var int ID пользователя
     */
    public $userId = 1;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Проверяем, находится ли объявление в избранном
        if (!isset($this->isFavorite)) {
            $this->isFavorite = MockFavorites::isInFavorites($this->propertyId, $this->userId);
        }

        // Регистрируем необходимый JavaScript
        $this->registerClientScript();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Подготавливаем заголовок в зависимости от состояния
        $title = $this->isFavorite ? $this->removeTitle : $this->addTitle;

        // Рендерим представление
        return $this->render('favorite-button', [
            'propertyId' => $this->propertyId,
            'isFavorite' => $this->isFavorite,
            'buttonClass' => $this->buttonClass,
            'title' => $title,
            'options' => $this->options,
        ]);
    }

    /**
     * Регистрирует необходимые клиентские скрипты
     */
    protected function registerClientScript()
    {
        $view = $this->getView();
        $view->registerJsFile('@web/js/components/favorite-button.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    }
}
