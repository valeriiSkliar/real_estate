<?php

namespace app\models;


use app\components\translations\TranslatableTrait;

/**
 * This is the model class for table "buttons".
 *
 * @property int $id
 * @property string|null $slug
 * @property string|null $name
 * @property int $priority
 * @property int $type
 * @property int $position
 * @property boolean $is_hidden
 * @property string|null $language
 * @property string|null $link
 * @property string|null $web_app_link
 */
class Buttons extends \yii\db\ActiveRecord
{
    use TranslatableTrait;

    public const TYPE_KEYBOARD = 0;
    public const TYPE_INLINE = 1;
    public const POSITION_NONE = 0;
    public const POSITION_MAIN = 1;
    public const POSITION_EDUCATION = 2;
    public const POSITION_REFERRAL = 3;
    public const POSITION_OTHERS = 4;
    public const POSITION_PAGES = 5;
    public const POSITION_LANGUAGES = 6;
    public const POSITION_DIALOG = 7;
    public const POSITION_TARIFFS = 8;
    public const POSITION_PAYMENT = 9;

    public const TYPES = [
        self::TYPE_KEYBOARD => 'Клавиатура',
        self::TYPE_INLINE => 'Инлайн',
    ];

    public const POSITIONS = [
        self::POSITION_NONE => 'Не назначено',
        self::POSITION_MAIN => 'Главная страница',
        self::POSITION_EDUCATION => 'Обучение',
        self::POSITION_OTHERS => 'Другие',
        self::POSITION_PAYMENT => 'После оплаты',
        self::POSITION_REFERRAL => 'Реф.кабинет',
        self::POSITION_TARIFFS => 'Тарифы',
    ];

    public const VISIBILITY = [
        0 => 'Активно',
        1 => 'Скрыто'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'buttons';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'name', 'language', 'link', 'web_app_link'], 'string'],
            [['position', 'type', 'priority'], 'integer'],
            [['is_hidden'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'ЧПУ',
            'name' => 'Название',
            'position' => 'Расположение',
            'type' => 'Тип',
            'priority' => 'Приоритет вывода',
            'is_hidden' => 'Видимость',
            'language' => 'Язык',
            'link' => 'Ссылка',
            'web_app_link' => 'Ссылка для приложения',
        ];
    }

    public static function createNewLanguageButton(Languages $language): Buttons
    {
        $button = new Buttons();
        $button->name = $language->name;
        $button->slug = 'language-' . $language->slug;
        $button->type = self::TYPE_INLINE;
        $button->position = self::POSITION_LANGUAGES;
        $button->priority = 1;
        $button->is_hidden = 1;
        $button->language = $language->slug;
        $button->save();

        return $button;
    }
}
