<?php

namespace app\components\services\bot;

use app\enums\BotButtonsKeys;
use app\enums\PaymentProviders;
use app\enums\Tariff;
use app\enums\ActiveStatuses;
use app\models\Buttons;
use app\models\Cities;
use app\models\Tariffs;
use Yii;

class ButtonService
{
    /**
     * @return array
     * Возвращает все начальные кнопки в соответствии с полем порядок в БД
     */
    public function getStartButtons(): array
    {
        return $this->getInlineButtons(Buttons::POSITION_MAIN);
    }

    /**
     * @param $slug
     * Возвращает кнопку по slug
     *
     * @return Buttons|null
     */
    public function getButton($slug): ?Buttons
    {
        return Buttons::findOne(['slug' => $slug, 'is_hidden' => 0]);
    }

    /**
     * @param $search
     * @return Buttons|null
     */
    public function searchButton($search): ?Buttons
    {
        /** @var ?Buttons $button */
        $button = Buttons::find()
            ->where(['or', ['name' => $search], ['slug' => $search]])
            ->andWhere(['language' => \Yii::$app->language])
            ->limit(1)
            ->one();

        return $button;
    }

    /**
     * @param $slug
     * Возвращает название кнопки по slug
     *
     * @return string|null
     */
    public function getButtonsName($slug): ?string
    {
        $button = Buttons::findOne(['slug' => $slug, 'is_hidden' => 0, 'language' => Yii::$app->language]);
        return trim($button->name);
    }

    /**
     * @param array $slugs
     * Возвращает название кнопок по массиву из slug
     * @param null  $position
     * @param bool  $languageStrict
     *
     * @return array
     */
    public function getButtonsNames(array $slugs, $position = null, bool $languageStrict = true): array
    {
        $query = Buttons::find()->where(['slug' => $slugs, 'is_hidden' => 0]);

        if ($languageStrict) {
            $query->andFilterWhere(['language' => Yii::$app->language]);
        }

        if ($position) {
            $query->andFilterWhere(['position' => $position]);
        }
        $buttons = $query->orderBy(['priority' => SORT_ASC])->asArray()->all();

        $result = [];
        $fetchedSlugs = [];

        foreach ($buttons as $button) {
            $result[$button['slug']] = trim($button['name']);
            $fetchedSlugs[] = $button['slug'];
        }

        $missingSlugs = array_diff($slugs, $fetchedSlugs);
        foreach ($missingSlugs as $slug) {
            $result[$slug] = $slug;
        }

        return $result;
    }

    /**
     * @param            $position
     * @param array|null $slugs
     * Возвращает название кнопок(reply keyboard) по массиву из slug
     * @param bool       $languageStrict
     *
     * @return array
     */
    public function getKeyboardButtons($position, array $slugs = null, bool $languageStrict = true): array
    {
        $query = Buttons::find()->where([
            'type'      => Buttons::TYPE_KEYBOARD,
            'position'  => $position,
            'is_hidden' => 0,
        ]);

        if ($languageStrict) {
            $query->andFilterWhere(['language' => Yii::$app->language]);
        }

        if ($slugs) {
            $query->andFilterWhere(['slug' => $slugs]);
        }
        $buttons = $query->orderBy(['priority' => SORT_ASC])->asArray()->all();

        $result = is_array($slugs) ? array_fill_keys($slugs, null) : [];

        foreach ($buttons as $button) {
            $result[$button['slug']] = trim($button['name']);
        }

        foreach ($result as $slug => &$name) {
            if (is_null($name)) {
                $name = $slug;
            }
        }

        return $result;
    }

    /**
     * Возвращает массив inline-кнопок для Telegram.
     *
     * @param string|null $position Позиция кнопок. Если null, фильтрация по позиции не выполняется.
     * @param array|null $slugs Массив slug кнопок для фильтрации. Если null, возвращаются все кнопки по позиции.
     * @param bool $languageStrict Фильтрация по языку.
     * @param int $chunkSize Количество кнопок в строке.
     *
     * @return array
     */
    public function getInlineButtons(string $position = null, array $slugs = null, bool $languageStrict = true, int $chunkSize = 1): array
    {
        $buttons = $this->getButtonsByFilters(Buttons::TYPE_INLINE, $position, $slugs, $languageStrict, $chunkSize);

        $result = is_array($slugs) ? array_fill_keys($slugs, null) : [];

        foreach ($buttons as $button) {
            $slug = $button['slug'];
            $result[$slug] = [
                'text'         => trim($button['name']),
                'web_app_link' => $button['web_app_link'] ?? null,
                'link'         => $button['link'] ?? null,
            ];
        }

        if ($slugs) {
            foreach ($result as $slug => $value) {
                if (empty($value)) {
                    $result[$slug] = ['text' => $slug];
                }
            }
        }

        return $this->prepareInlineButtons($result, $chunkSize);
    }

    public function getButtonsByFilters(string $type, string $position = null, array $slugs = null, bool $languageStrict = true, int $chunkSize = 1): array
    {
        $query = Buttons::find()
            ->where(['type' => $type, 'is_hidden' => 0]);

        if ($position !== null) {
            $query->andWhere(['position' => $position]);
        }

        if ($languageStrict) {
            $query->andWhere(['language' => Yii::$app->language]);
        }

        if (!empty($slugs)) {
            $query->andWhere(['slug' => $slugs]);
        }

        return $query
            ->select(['slug', 'name', 'link', 'web_app_link'])
            ->orderBy(['priority' => SORT_ASC])
            ->asArray()
            ->all();
    }

    private function formatButton(string $slug, array $buttonData): array
    {
        if (!empty($buttonData['web_app_link'])) {
            return [
                'text'    => $buttonData['text'],
                'web_app' => $buttonData['web_app_link'],
            ];
        }

        if (!empty($buttonData['link'])) {
            return [
                'text' => $buttonData['text'],
                'url'  => $buttonData['link'],
            ];
        }

        return [
            'text'          => $buttonData['text'],
            'callback_data' => $slug,
        ];
    }

    public function prepareInlineButtons(array $buttons, int $chunkSize = 1): array
    {
        $preparedButtons = [];
        $chunks = array_chunk($buttons, $chunkSize, true);

        foreach ($chunks as $chunk) {
            $row = [];

            foreach ($chunk as $slug => $buttonData) {
                $row[] = $this->formatButton($slug, $buttonData);
            }

            $preparedButtons[] = $row;
        }

        return $preparedButtons;
    }

    /**
     * @param $key
     * @param $value
     * Устанавливает значение в кэш
     *
     * @return void
     */
    public function setCache($key, $value): void
    {
        Yii::$app->cache->set($key, $value);
    }

    /**
     * @param $key
     * Возвращает значение из кэша
     *
     * @return mixed
     */
    public function getCache($key): mixed
    {
        return Yii::$app->cache->get($key);
    }

    /**
     * @param $key
     * Чистит кэш по ключу
     *
     * @return bool
     */
    public function deleteCache($key): bool
    {
        return Yii::$app->cache->delete($key);
    }

    /**
     * Устанавливает значение последней нажатой кнопки в кэш
     * @param $value
     * @param $userId
     * @return void
     */
    public function setLastButton($value, $userId): void
    {
        $this->setCache("last_button_$userId", $value);
    }

    /**
     * Возвращает значение последней нажатой кнопки из кэша
     * @param $userId
     * @return mixed
     */
    public function getLastButton($userId): mixed
    {
        return $this->getCache("last_button_$userId");
    }

    public function clearLastButton($userId): void
    {
        $this->deleteCache("last_button_$userId");
    }

    public function getTariffButtons(): array
    {
        $tariffs = Tariffs::find()->where(['status' => ActiveStatuses::ACTIVE->value])->all();
        $result = [];

        /** @var Tariffs $tariff */
        foreach ($tariffs as $tariff) {
            $result[BotButtonsKeys::TARIFF_PREFIX->value . $tariff->id] = trim($tariff->name);
        }

        return $result;
    }

    public function getTariffTypeButtons(int $chunkSize = 1): array
    {
        $tariffTypes = Tariff::getTariffs();
        $result = [];

        foreach ($tariffTypes as $id => $name) {
            $result[BotButtonsKeys::TARIFF_TYPE_PREFIX->value . $id] = ['text' => trim($name)];
        }

        return $this->prepareInlineButtons($result, $chunkSize) ;
    }

    public function getPaymentMethodButtons(int $chunkSize = 1): array
    {
        $methods = PaymentProviders::getAllCases();
        $result = [];

        foreach ($methods as $key => $name) {
            $buttonKey = BotButtonsKeys::PAYMENT_METHOD_PREFIX->value . $key;

            $result[$buttonKey] = ['text' => $name];
        }

        return $this->prepareInlineButtons($result, $chunkSize) ;
    }

    public function getCityButtons($chunkSize = 1): array
    {
        $cities = Cities::getActiveCities();
        $result = [];

        /** @var Cities $city */
        foreach ($cities as $city) {
            $result[BotButtonsKeys::CITY_PREFIX->value . $city->id] = ['text' => trim($city->name)];
        }

        return $this->prepareInlineButtons($result, $chunkSize);
    }
}