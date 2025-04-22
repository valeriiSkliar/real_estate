<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "button_clicks".
 *
 * @property int $id
 * @property string $name
 * @property int $counter
 * @property string|null $date
 */
class ButtonClicks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'button_clicks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['counter'], 'integer'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'counter' => 'Counter',
            'date' => 'Date',
        ];
    }
}
