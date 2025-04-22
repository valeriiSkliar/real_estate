<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "selections".
 *
 * @property int $id
 * @property string $name
 * @property int $realtor_id
 * @property string|null $advertisement_list
 */
class Selections extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'selections';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['advertisement_list'], 'default', 'value' => null],
            [['name', 'realtor_id'], 'required'],
            [['realtor_id'], 'integer'],
            [['advertisement_list'], 'safe'],
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
            'realtor_id' => 'Realtor ID',
            'advertisement_list' => 'Advertisement List',
        ];
    }

}
