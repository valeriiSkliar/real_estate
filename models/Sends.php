<?php

namespace app\models;

use app\components\telegram\interfaces\TelegramMediaInterface;
use app\helpers\UploadImageValidateHelper;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "sends".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property string $file_url
 * @property string $destination
 * @property string $date
 * @property bool $is_regular
 * @property integer $provider
 * @property string $language
 * @property bool $is_single
 * @property string $recipient
 * @property string $video_url
 * @property string $audio_url
 * @property string $image_url
 */
class Sends extends ActiveRecord implements TelegramMediaInterface
{
    public static array $STATUSES = ['В процессе','Отправлено'];

    public const STATUS_PENDING = 0;
    public const STATUS_SUCCESS = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;
    /**
     * @var UploadedFile
     */
    public $videoFile;
    /**
     * @var UploadedFile
     */
    public $audioFile;
    /**
     * @var UploadedFile
     */
    public $fileUpload;
    public mixed $destinationArray = [];
    public ?BotUsers $recipientEntity = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sends';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['description', 'date', 'language','file_url',  'image_url', 'video_url', 'audio_url'], 'string'],
            [['status', 'is_regular', 'provider'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true],
            [['fileUpload'], 'file', 'skipOnEmpty' => true],
            [['audioFile'], 'file', 'skipOnEmpty' => true],
            [['videoFile'], 'file', 'skipOnEmpty' => true],
            [['destination'], 'string', 'max' => 255],
            [['destinationArray', 'is_single', 'recipient'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'description' => 'Текст',
            'status' => 'Status',
            'imageFile' => 'Файл',
            'file_url' => 'Файл',
            'destinationArray' => 'Получатели',
            'destination' => 'Получатели',
            'date' => 'Дата',
            'is_regular' => 'Еженедельно',
            'provider' => 'Провайдер',
            'language' => 'Язык',
            'is_single' => 'Единичная рассылка',
           'recipient' => 'Получатель',
            'video_url' => 'Видео',
            'audio_url' => 'Аудио',
            'image_url' => 'Изображение',
            'fileUpload' => 'Файл',
            'audioFile' => 'Аудио',
            'videoFile' => 'Видео',
        ];
    }

    public function upload(): bool
    {
        if ($this->validate()) {
            // Полный путь к директории
            $uploadPath = Yii::getAlias("@app/web/uploads/sends");

            // Проверяем, существует ли директория и является ли она директорией
            if (!is_dir($uploadPath)) {
                // Пытаемся создать директорию с правами 0777 и рекурсивно
                if (!mkdir($uploadPath, 0777, true) && !is_dir($uploadPath)) {
                    Yii::error(sprintf('Директория "%s" не была создана', $uploadPath));
                    throw new \RuntimeException(sprintf('Директория "%s" не была создана', $uploadPath));
                }
            }

            // Массив конфигураций для различных типов файлов
            $files = [
                [
                    'attribute' => 'imageFile',        // Имя свойства модели
                    'dbAttribute' => 'image_url',          // Поле в базе данных
                    'suffix' => '',                    // Суффикс к имени файла
                ],
                [
                    'attribute' => 'fileUpload',
                    'dbAttribute' => 'file_url',
                    'suffix' => '-file',
                ],
                [
                    'attribute' => 'videoFile',
                    'dbAttribute' => 'video_url',
                    'suffix' => '-video',
                ],
                [
                    'attribute' => 'audioFile',
                    'dbAttribute' => 'audio_url',
                    'suffix' => '-audio',
                ],
            ];

            foreach ($files as $fileConfig) {
                $fileAttribute = $fileConfig['attribute'];
                $dbAttribute = $fileConfig['dbAttribute'];
                $suffix = $fileConfig['suffix'];

                if (
                    isset($this->$fileAttribute)
                    && $this->$fileAttribute instanceof UploadedFile
                    && $this->$fileAttribute->error === UPLOAD_ERR_OK
                ) {
                    // Формируем путь для сохранения файла
                    $path = '/uploads/sends/' . $this->id . $suffix . '.' . $this->$fileAttribute->extension;

                    // Полный путь для сохранения файла
                    $fullPath = Yii::getAlias("@app/web") . $path;

                    // Пытаемся сохранить файл
                    if ($this->$fileAttribute->saveAs($fullPath)) {
                        // Обновляем соответствующий атрибут в базе данных
                        if (!$this->updateAttributes([$dbAttribute => $path])) {
                            Yii::error("Не удалось обновить атрибут {$dbAttribute} в базе данных.");
                        }
                    } else {
                        Yii::error("Не удалось сохранить файл в {$fullPath}");
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if (is_array($this->destinationArray) && !empty($this->destinationArray)) {
                $this->destination = implode(',', $this->destinationArray);
            }

            return true;
        }

        return false;
    }

    public static function getPendingSends(): array
    {
        return self::find()
            ->where(['status' => self::STATUS_PENDING])
            ->andWhere(['<=', 'date', date('Y-m-d H:i:s')])
            ->all();
    }

    public function updateSentEntity(): void
    {
        // Если рассылка регулярная, обновляем дату на следующий период
        if ($this->is_regular) {
            $this->date = date('Y-m-d H:i:s', strtotime($this->date . ' +1 week'));
        } else {
            $this->status = self::STATUS_SUCCESS;
        }
        $this->save();
    }

    public function getRecipientEntity(): array|ActiveRecord
    {
        return $this->recipientEntity ?? $this->getRecipientUser()->one();
    }

    public function getRecipientUser(): ActiveQuery
    {
        return $this->hasOne(BotUsers::class, ['id' => 'recipient']);
    }

    public function getAudio(): ?string
    {
        return $this->audio_url;
    }

    public function getFile(): ?string
    {
        return $this->file_url;
    }

    public function getVideo(): ?string
    {
        return $this->video_url;
    }

    public function getImage(): ?string
    {
        return $this->image_url;
    }
}
