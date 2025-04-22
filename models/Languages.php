<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "languages".
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property boolean $is_active
 */
class Languages extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const TRANSLATORS = [
        'google' => 'Google Translate',
        'deepl' => 'DeepL Translate',
        'deepl-free' => 'DeepL Translate (free)',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'languages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'name', 'is_active'], 'required'],
            [['slug', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'ISO code',
            'name' => 'Название',
            'is_active' => 'Статус',
        ];
    }

    public static function getActiveLanguages(): array
    {
        return self::find()
            ->where(['is_active' => self::STATUS_ACTIVE])
            ->all();
    }

    public static function getAllLanguages(): array
    {
        return self::find()->all();
    }
}
