<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "advertisements".
 *
 * @property int $id
 * @property string $property_type
 * @property string $trade_type
 * @property string $source
 * @property string|null $realtor_phone
 * @property string|null $address
 * @property string|null $clean_description
 * @property string|null $raw_description
 * @property string|null $property_name
 * @property string|null $locality
 * @property string|null $district
 * @property int|null $room_quantity
 * @property int|null $property_area
 * @property int|null $land_area
 * @property string|null $condition
 * @property int|null $price
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property AdvertisementImages[] $advertisementImages
 */
class Advertisements extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advertisements';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['property_type', 'trade_type', 'source'], 'required'],
            [['clean_description', 'raw_description'], 'string'],
            [['room_quantity', 'property_area', 'land_area', 'price'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['property_type', 'trade_type', 'source', 'realtor_phone', 'address', 'property_name', 'locality', 'district', 'condition'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'property_type' => 'Тип недвижимости',
            'trade_type' => 'Тип сделки',
            'source' => 'Источник',
            'realtor_phone' => 'Телефон риэлтора',
            'address' => 'Адрес',
            'clean_description' => 'Чистое описание',
            'raw_description' => 'Исходное описание',
            'property_name' => 'Название недвижимости',
            'locality' => 'Населённый пункт',
            'district' => 'Район',
            'room_quantity' => 'Количество комнат',
            'property_area' => 'Площадь недвижимости',
            'land_area' => 'Площадь участка',
            'condition' => 'Состояние',
            'price' => 'Цена',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Gets query for [[AdvertisementImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdvertisementImages()
    {
        return $this->hasMany(AdvertisementImages::class, ['advertisement_id' => 'id']);
    }
}
