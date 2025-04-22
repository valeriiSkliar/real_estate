<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "advertisement_sections".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $sort
 * @property string $type
 */
class AdvertisementSections extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_APP = 'app';
    const TYPE_HOUSE = 'house';
    const TYPE_LAND = 'land';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advertisement_sections';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort'], 'default', 'value' => 0],
            [['name', 'slug', 'type'], 'required'],
            [['sort'], 'integer'],
            [['type'], 'string'],
            [['name', 'slug'], 'string', 'max' => 255],
            ['type', 'in', 'range' => array_keys(self::optsType())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'sort' => 'Sort',
            'type' => 'Type',
        ];
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_APP => 'app',
            self::TYPE_HOUSE => 'house',
            self::TYPE_LAND => 'land',
        ];
    }

    /**
     * @return string
     */
    public function displayType()
    {
        return self::optsType()[$this->type];
    }

    /**
     * @return bool
     */
    public function isTypeApp()
    {
        return $this->type === self::TYPE_APP;
    }

    public function setTypeToApp()
    {
        $this->type = self::TYPE_APP;
    }

    /**
     * @return bool
     */
    public function isTypeHouse()
    {
        return $this->type === self::TYPE_HOUSE;
    }

    public function setTypeToHouse()
    {
        $this->type = self::TYPE_HOUSE;
    }

    /**
     * @return bool
     */
    public function isTypeLand()
    {
        return $this->type === self::TYPE_LAND;
    }

    public function setTypeToLand()
    {
        $this->type = self::TYPE_LAND;
    }
}
