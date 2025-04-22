<?php

namespace app\models;

use app\helpers\UploadImageValidateHelper;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "user_info".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $description
 * @property string|null $photo_url
 * @property string|null $language
 * @property string $name
 * @property string $phone
 * @property string $telegram
 * @property string $email
 * @property string $whatsapp
 *
 * @property BotUsers $user
 */
class UserInfo extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','name','description'], 'required'],
            [['user_id'], 'integer'],
            [['description'], 'string'],
            [['photo_url', 'language'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::class, 'targetAttribute' => ['user_id' => 'id']],
            [['name', 'phone', 'telegram', 'email', 'whatsapp'],'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'description' => 'Description',
            'photo_url' => 'Фото',
            'language' => 'Язык',
            'name' => 'Имя',
            'phone' => 'Телефон',
            'telegram' => 'Телеграм',
            'email' => 'Почта',
            'whatsapp' => 'Whatsapp',
        ];
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function upload()
    {
        if ($this->validate()) {
            if(isset($this->imageFile->name)) {
                $path = UploadImageValidateHelper::upload($this->imageFile, 'user-info', $this->id);
                $this->updateAttributes(['photo_url' => $path]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'user_id']);
    }
}
