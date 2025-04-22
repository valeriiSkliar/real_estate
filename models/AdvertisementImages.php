<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "advertisement_images".
 *
 * @property int $id
 * @property int $advertisement_id
 * @property string $image
 *
 * @property Advertisements $advertisement
 */
class AdvertisementImages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advertisement_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['advertisement_id', 'image'], 'required'],
            [['advertisement_id'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['advertisement_id'], 'exist', 'skipOnError' => true, 'targetClass' => Advertisements::class, 'targetAttribute' => ['advertisement_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertisement_id' => 'Advertisement ID',
            'image' => 'Image',
        ];
    }

    /**
     * Gets query for [[Advertisement]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdvertisement()
    {
        return $this->hasOne(Advertisements::class, ['id' => 'advertisement_id']);
    }
}
