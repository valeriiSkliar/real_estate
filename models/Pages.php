<?php

namespace app\models;

use app\components\telegram\interfaces\TelegramMediaInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

/**
 * This is the model class for table "page".
 *
 * @property int         $id
 * @property string|null $command
 * @property string|null $h1
 * @property string|null $image
 * @property string|null $file
 * @property string|null $text
 * @property string|null $language
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $video
 * @property string|null $audio
 */
class Pages extends \yii\db\ActiveRecord implements TelegramMediaInterface
{
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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['h1', 'command'], 'required'],
            [['text', 'meta_title', 'meta_description', 'meta_keywords', 'file', 'video', 'audio'], 'string'],
            [['command', 'h1', 'image', 'language'], 'string', 'max' => 255],
            [['command', 'language'], 'unique', 'targetAttribute' => ['command', 'language'], 'message' => 'Комбинация команды и языка должна быть уникальной.'],
            [
                ['imageFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, gif',
                'maxSize' => 10 * 1024 * 1024, // 10 МБ
                'tooBig' => 'Размер изображения не должен превышать 10 МБ.',
            ],
            [
                ['fileUpload'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf, doc, docx, txt',
                'maxSize' => 20 * 1024 * 1024, // 20 МБ
                'tooBig' => 'Размер файла не должен превышать 20 МБ.',
            ],
            [
                ['audioFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'mp3, wav, ogg',
                'maxSize' => 15 * 1024 * 1024, // 15 МБ
                'tooBig' => 'Размер аудио не должен превышать 15 МБ.',
            ],
            [
                ['videoFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'mp4, avi, mov, mkv',
                'maxSize' => 50 * 1024 * 1024, // 50 МБ
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'command'          => 'Command',
            'h1'               => 'Заголовок',
            'image'            => 'Изображение',
            'text'             => 'Текст',
            'language'         => 'Язык',
            'imageFile'        => 'Изображение',
            'meta_title'       => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords'    => 'Meta Keywords',
            'file'             => 'Файл',
            'fileUpload'             => 'Файл',
            'video'             => 'Видео',
            'audio'             => 'Аудио',
            'audioFile'        => 'Аудио',
            'videoFile'        => 'Видео',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            // Сохраняем запись в базе данных
            if (!$this->save()) {
                Yii::error('Не удалось сохранить запись.');
                return false;
            }

            // Полный путь к директории
            $uploadPath = Yii::getAlias("@app/web/uploads/pages");

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
                    'dbAttribute' => 'image',          // Поле в базе данных
                    'suffix' => '',                    // Суффикс к имени файла
                ],
                [
                    'attribute' => 'fileUpload',
                    'dbAttribute' => 'file',
                    'suffix' => '-file',
                ],
                 [
                     'attribute' => 'videoFile',
                     'dbAttribute' => 'video',
                     'suffix' => '-video',
                 ],
                 [
                     'attribute' => 'audioFile',
                     'dbAttribute' => 'audio',
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
                    $path = '/uploads/pages/' . $this->id . $suffix . '.' . $this->$fileAttribute->extension;

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

    public function replaceReferralData(BotUsers $user): void
    {
        $this->h1 = str_replace(
            ['{balance}', '{expireDate}', '{phone}', '{email}', '{name}'],
            [$user->bonus, $user->paid_until],
            $this->h1
        );
        $this->text = str_replace(
            ['{balance}', '{expireDate}', '{phone}', '{email}', '{name}'],
            [$user->bonus, $user->paid_until, $user->phone, $user->email, $user->fio],
            $this->text
        );
    }

    /**
     * Получает переведенные сущности
     *
     * @return ActiveQuery
     */
    public function getTranslatedEntities(): ActiveQuery
    {
        return $this->hasMany(static::class, ['command' => 'command'])
            ->andFilterWhere(['<>', 'id', $this->id]);
    }

    public function getAudio(): ?string
    {
        return $this->audio;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
