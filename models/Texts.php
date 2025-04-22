<?php

namespace app\models;

use app\components\translations\TranslatableTrait;
use app\helpers\ErrorLogHelper;
use Yii;

/**
 * This is the model class for table "texts".
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $language
 */
class Texts extends \yii\db\ActiveRecord
{
    use TranslatableTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'texts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'name'], 'required'],
            [['slug', 'language'], 'string', 'max' => 255],
            [['name'], 'string',],
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
            'name' => 'Текст',
            'language' => 'Язык',
        ];
    }

    public static function getLabels($language)
    {
        $cacheKey = "labels_{$language}";
        $cacheDuration = 3600;

        return Yii::$app->cache->getOrSet($cacheKey, function () use ($language) {
            return self::find()
                ->select(['name'])
                ->where(['language' => $language])
                ->indexBy('slug')
                ->column();
        }, $cacheDuration);
    }

    public static function getLabel($slug, $language = null): string
    {
        if ($language === null) {
           $language =  Yii::$app->language;
        }

        $labels = self::getLabels($language);

        if (!isset($labels[$slug])) {
            ErrorLogHelper::logBotInfo("Текст с slug $slug не найден в языке $language");

            return '';
        }

        return $labels[$slug];
    }
}
