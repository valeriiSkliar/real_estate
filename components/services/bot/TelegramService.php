<?php

namespace app\components\services\bot;

use app\components\StringHelper;
use app\components\telegram\handlers\TelegramApiHandler;
use app\components\telegram\interfaces\TelegramMediaInterface;
use app\enums\BotTextKeys;
use app\helpers\ErrorLogHelper;
use app\helpers\UploadImageValidateHelper;
use app\models\BotUsers;
use app\models\Buttons;
use app\models\dto\TelegramUserDto;
use app\models\Pages;
use app\models\Texts;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use League\HTMLToMarkdown\HtmlConverter;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Throwable;
use Yii;

class TelegramService
{
    public array $labels;
    private string $token;
    private TelegramUserDto $telegramUserDto;

    public function __construct(
        private readonly ButtonService $buttonService,
        private readonly TelegramApiHandler $telegram,
        private readonly TelegramDataParseService $telegramDataParseService,
    ) {
        $this->labels = Texts::getLabels(Yii::$app->language);
        $this->token = Yii::$app->params['bot_token'];
        $this->telegramUserDto = $this->telegramDataParseService->getUserDto();
    }

    /**
     * Используется для установки ID пользователя, когда включаем сервис из кода, а не через Telegram
     *
     * @param $userId
     * @return void
     */
    public function setTelegramUserId($userId): void
    {
       $this->telegramUserDto = new TelegramUserDto([
           'userId' => $userId,
           'language' => Yii::$app->language,
       ]);

        $this->labels = Texts::getLabels(Yii::$app->language);
    }

    public function getTelegramUserDto(): TelegramUserDto
    {
        return $this->telegramUserDto;
    }

    /**
     * Отправляет сообщение в телеграм этому пользователю
     *
     * @param $text
     * @return void
     * @throws TelegramSDKException
     */
    public function sendMessage($text): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->telegramUserDto->getUserId(),
            'text'    => $text,
        ]);
    }


    /**
     * Отправляет сообщение в телеграм этому пользователю(HTML)
     *
     * @param $text
     * @param null $userId
     * @return void
     * @throws TelegramSDKException
     * @throws Throwable
     */
    public function sendMessageByMarkup($text, $userId = null): void
    {
        try {
            $convertedText = $this->convertHtmlToTelegramHtml($text);

            $this->telegram->sendMessage([
                'chat_id' => $userId ?? $this->telegramUserDto->getUserId(),
                'text'    => $convertedText,
                'parse_mode'   => 'html',
            ]);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage(). $e->getTraceAsString(),
                "Ошибка при отправке сообщения в Telegram пользователю: {$this->telegramUserDto->getUserId()}");

            $sentText = $convertedText ?? '!!!Текст не был переведен в Telegram!!!';

            ErrorLogHelper::logBotInfo($sentText);

            throw $e;
        }
    }

    /**
     * Отправляет сообщение в телеграм этому пользователю (markdown)
     *
     * @param $text
     * @return void
     * @throws TelegramSDKException
     */
    public function sendMessageByMarkDown($text): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->telegramUserDto->getUserId(),
            'text'    => $text,
            'parse_mode'   => 'markdown',
        ]);
    }


    /**
     * Отправляет сообщение в телеграм указанному пользователю
     *
     * @param $text
     * @param $userId
     * @return void
     * @throws TelegramSDKException
     */

    public function sendMessageTo($text, $userId): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $userId,
            'text'    => $text,
        ]);
    }

    /**
     * Отправляет сообщение в телеграм с кнопками этому пользователю
     *
     * @param $text
     * @param $keyboard
     * @return void
     * @throws TelegramSDKException|JsonException
     */
    public function sendMessageWithKeyboard($text, $keyboard): void
    {
        $this->telegram->sendMessage([
            'chat_id'      => $this->telegramUserDto->getUserId(),
            'text'         => $text,
            'reply_markup' => json_encode($keyboard, JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Отправляет сообщение в телеграм с кнопками указанному пользователю
     *
     * @param $text
     * @param $keyboard
     * @param $userId
     * @return void
     * @throws TelegramSDKException|JsonException
     */
    public function sendMessageWithKeyboardTo($text, $keyboard, $userId): void
    {
        $this->telegram->sendMessage([
            'chat_id'      => $userId,
            'text'         => $text,
            'reply_markup' => json_encode($keyboard, JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * @param $text
     * @return void
     * @throws TelegramSDKException
     * @throws JsonException
     */
    public function sendMessageWithStartKeyboard($text): void
    {
        $keyboard = $this->buttonService->getStartButtons();

        $this->telegram->sendMessage([
            'chat_id'      => $this->telegramUserDto->getUserId(),
            'text'         => $text,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ], JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Отправляет сообщение в телеграм со стандартными кнопками указанному пользователю
     *
     * @param $text
     * @param $userId
     * @return void
     * @throws TelegramSDKException|JsonException
     */
    public function sendMessageWithStartKeyboardTo($text, $userId): void
    {
        $keyboard = $this->buttonService->getStartButtons();

        $this->telegram->sendMessage([
            'chat_id'      => $userId,
            'text'         => $text,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ], JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Отправляет сообщение в телеграм с кнопками с html разметкой этому пользователю
     *
     * @param $text
     * @param $keyboard
     * @return void
     * @throws JsonException|TelegramSDKException
     */
    public function sendMessageWithKeyboardByMarkup($text, $keyboard): void
    {
        $this->telegram->sendMessage([
            'chat_id'      => $this->telegramUserDto->getUserId(),
            'text'         => $text,
            'parse_mode'   => 'html',
            'reply_markup' => json_encode($keyboard, JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Отправляет сообщение в телеграм с кнопками(в самом сообщении) этому пользователю
     *
     * @param $text
     * @param $inlineKeyboard
     * @return void
     * @throws JsonException|TelegramSDKException
     */
    public function sendMessageWithInlineKeyboard($text, $inlineKeyboard): void
    {
        $this->telegram->sendMessage([
            'chat_id'      => $this->telegramUserDto->getUserId(),
            'text'         => $text,
            'reply_markup' => json_encode([
                'inline_keyboard' => $inlineKeyboard,
            ], JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Отправляет сообщение в телеграм с кнопками(в самом сообщении) этому пользователю
     *
     * @param $text
     * @param $inlineKeyboard
     * @return void
     * @throws JsonException|TelegramSDKException|Throwable
     */
    public function sendMessageWithInlineKeyboardByMarkup($text, $inlineKeyboard): void
    {
        try {
            $convertedText = $this->convertHtmlToTelegramHtml($text);

            $this->telegram->sendMessage([
                'chat_id'      => $this->telegramUserDto->getUserId(),
                'text'         => $convertedText,
                'parse_mode'   => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => $inlineKeyboard,
                ], JSON_THROW_ON_ERROR),
            ]);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage(). $e->getTraceAsString(),
                "Ошибка при отправке сообщения в Telegram пользователю: {$this->telegramUserDto->getUserId()}");

            $sentText = $convertedText ?? '!!!Текст не был переведен в Telegram!!!';

            ErrorLogHelper::logBotInfo($sentText);

            throw $e;
        }
    }

    /**
     * Отправляет сообщение об ошибке пользователю.
     *
     * @return void
     * @throws Throwable
     */
    public function sendErrorMessage(): void
    {
        $text = $this->labels['error-message'];
        $this->sendMenu($text);
    }

    /**
     * Отправляет изображение
     *
     * @param $fileUrl
     * @return void
     */
    public function sendPhoto($fileUrl, $isFile = true): void
    {
        try {
            if (!$fileUrl) {
                return;
            }

            $path = $isFile ? Yii::getAlias('@webRoot') . $fileUrl : $fileUrl;

            if ($isFile && !is_readable($path)) {
                ErrorLogHelper::logBotInfo('Ошибка(файл не читаемый) при отправке изображения в Telegram пользователю ' . $this->telegramUserDto->getUserId());

                return;
            }

            $photo = InputFile::create($path);
            $this->telegram->sendPhoto(['chat_id' => $this->telegramUserDto->getUserId(), 'photo' => $photo]);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage(). $e->getTraceAsString(),
                'Ошибка при отправке изображения в Telegram пользователю '. $this->telegramUserDto->getUserId());
            ErrorLogHelper::logBotInfo($fileUrl);
        }
    }

    /**
     * Отправляет изображение
     *
     * @param $fileUrl
     * @return void
     */
    public function sendDocument($fileUrl): void
    {
        try {
            if (!$fileUrl) {
                return;
            }

            $path = Yii::getAlias('@webRoot') . $fileUrl;

            if (is_readable($path)) {
                $document = InputFile::create($path);

                $this->telegram->sendDocument([
                    'chat_id' => $this->telegramUserDto->getUserId(),
                    'document' => $document
                ]);
            }
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage(). $e->getTraceAsString(),
                'Ошибка при отправке изображения в Telegram пользователю '. $this->telegramUserDto->getUserId());
            ErrorLogHelper::logBotInfo($fileUrl);
        }
    }

    /**
     * Сохраняет фотографию пользователя
     *
     * @param $message
     * @return string
     * @throws GuzzleException
     * @throws JsonException
     */
    public function savePhoto($message): string
    {
        if (isset($message['document']) || isset($message['photo'])) {

            $file = $message['document'] ?? end($message['photo']);
            $fileId = $file['file_id'];
            $fileUrl = $this->getFileUrl($fileId);
            $imagePath = '/uploads/user-info/' . $this->telegramUserDto->getUserId() . '.jpg';
            $absoluteImagePath = Yii::getAlias("@app/web") . $imagePath;

            if (file_put_contents($absoluteImagePath, file_get_contents($fileUrl))) {
                return $imagePath;
            }
        }

        return "";
    }

    /**
     * Получает ссылку на фотографию для дальнейшего сохранения
     *
     * @param $fileId
     * @return string
     * @throws GuzzleException|JsonException
     */

    public function getFileUrl($fileId): string
    {
        $token = $this->token;
        $fileApiUrl = "https://api.telegram.org/bot" . $token . "/getFile?file_id=" . $fileId;

        $guzzleClient = new Client();
        $response = $guzzleClient->get($fileApiUrl);
        $fileInfo = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($fileInfo['result']['file_path'])) {
            return "https://api.telegram.org/file/bot" . $token . "/" . $fileInfo['result']['file_path'];
        }

        return "";
    }

    /**
     * Выводит меню
     *
     * @param null $text
     * @return void
     * @throws Throwable
     */
    public function sendMenu($text = null): void
    {
        try {
            $text = $text ?: Texts::getLabel(BotTextKeys::MENU->value);
            $result = $this->buttonService->getInlineButtons(Buttons::POSITION_MAIN);

            if ($result) {
                $this->sendMessageWithInlineKeyboardByMarkup($text, $result);
            }
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                json_encode($e->getMessage(). $e->getTraceAsString(), JSON_THROW_ON_ERROR),
                'Ошибка при отправке меню id=' . $this->telegramUserDto->getUserId()
            );
        }
    }

    /**
     * Вытягивает из базы страницу и отправляет вместе с кнопками
     *
     * @param string $command
     * @param bool $menuRequired
     * @param array $buttons
     * @return void
     * @throws JsonException
     * @throws TelegramSDKException
     * @throws Throwable
     */
    public function sendPage(
        string $command,
        bool $menuRequired = false,
        array $buttons = [],
        Pages $page = null
    ): void {
        if ($page === null) {
            $page = Pages::findOne(['command' => $command, 'language' => Yii::$app->language]);
        }

        if (!$page) {
            ErrorLogHelper::logBotInfo("Страница $command НЕ найдена");

            return;
        }

        $text = "<b>" . $page->h1 . "</b>" . PHP_EOL . PHP_EOL . $page->text;

        $this->sendMedia($page);

        if ($menuRequired) {
            $this->sendMenu($text);

            return;
        }else{
            if ($buttons){
                $this->sendMessageWithInlineKeyboardByMarkup($text, $buttons);

                return;
            }

            $this->sendMessageByMarkup($text);
        }

        return;
    }

    public function sendMedia(TelegramMediaInterface $media): void
    {
        $this->sendAudio($media->getAudio());

        $this->sendVideoNote($media->getVideo());

        $this->sendDocument($media->getFile());

        $this->sendPhoto($media->getImage());
    }

    public function convertHtmlToMarkdown($html): string
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'use_autolinks' => false,
            'hard_break' => true,
        ]);

        // Добавляем обработку для тега <strong>
        $converter->getConfig()->setOption('header_style', false);
        $converter->getConfig()->setOption('bold_style', '*');
        $converter->getConfig()->setOption('italic_style', '_');

        return $converter->convert($html);
    }

    public function convertHtmlToTelegramHtml($html): string
    {
        $decodedHtml = html_entity_decode($html);

        return StringHelper::transformToTelegramValidText($decodedHtml);
    }

    public function incrementButtonClickCount($buttonName): void
    {
        $cache = Yii::$app->cache;
        $cacheKey = "button_click_count_" . $buttonName;

        // Сохраняем ключи кликов
        $keys = $cache->get('button_click_keys') ?? [];

        if (!isset($keys[$cacheKey])) {
            $keys[$cacheKey] = 0;
        }

        ++$keys[$cacheKey];
        $cache->set('button_click_keys', $keys, 86500);
    }

    public function saveUserPhoto(): ?string
    {
        try {
            // Получаем фото пользователя
            $response = $this->telegram->getUserProfilePhotos(['user_id' => $this->telegramUserDto->getUserId()]);

            // Проверяем, есть ли фото
            if ($response->get('total_count') > 0) {
                $photos = $response->get('photos');

                // Проверяем, что массив фото не пустой
                if (count($photos[0]) > 0) {
                    // Получаем 'file_id' первого фото в массиве
                    $fileId = $photos[0][0]['file_id'];

                    // Получаем информацию о файле
                    $fileResponse = $this->telegram->getFile(['file_id' => $fileId]);

                    // Получаем путь к файлу на серверах Telegram
                    $filePath = $fileResponse->get('file_path');

                    // Формируем URL для скачивания файла
                    $fileUrl = "https://api.telegram.org/file/bot" . $this->token . "/" . $filePath;

                    // Путь для сохранения файла на сервере
                    $folder = Yii::getAlias('@webroot') . '/uploads/tg/';
                    UploadImageValidateHelper::makeDirectory($folder);
                    $savePath = $folder . time() . basename($filePath);

                    // Скачиваем и сохраняем файл
                    if (file_put_contents($savePath, file_get_contents($fileUrl))) {
                        return '/uploads/tg/' . time() . basename($filePath);
                    }
                }
            }

            return null;
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(json_encode($e->getMessage(). $e->getTraceAsString(), JSON_THROW_ON_ERROR), 'Ошибка при получения фото id=' . $this->telegramUserDto->getUserId());

            return null;
        }
    }

    /**
     * @param $string
     * Функция возвращает все цифры и тип из строки.
     *
     * @return mixed
     */
    public function getParametersFromString($string): array
    {
        $inputString = preg_replace('/\/start\s?/', '', $string);

        if (!$inputString){
            return [];
        }

        if (is_numeric($inputString)) {
            return [
                'param' => null,
                'id' => $inputString,
            ];
        }

        return [
            'param' => $inputString,
            'id' => null,
        ];
    }

    /**
     * @param $id
     * Возвращает пользователя из БД по телеграм айди
     *
     * @return BotUsers|null
     */
    public function getUserFromDb($id): ?BotUsers
    {
        if ($id && is_numeric($id)) {
            return BotUsers::find()->where(['uid' => $id])->with('parent')->limit(1)->one();
        }

        return null;
    }

    /**
     * @param $document
     * Отправляет файл
     * @return string
     * @throws JsonException
     */
    public function sendCsv($document): string
    {
        try {
            return $this->telegram->sendDocument([
                'chat_id' => $this->telegramUserDto->getUserId(),
                'document' => $document,
            ]);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(json_encode($e->getMessage(). $e->getTraceAsString(), JSON_THROW_ON_ERROR), 'Ошибка при отправке CSV id='. $this->telegramUserDto->getUserId());

            return '';
        }
    }

    /**
     * Отправляет аудио файл пользователю в Telegram
     *
     * @param string|null $fileUrl Путь к аудио файлу или URL
     * @param bool $isFile Указывает, является ли $fileUrl локальным путем (true) или внешним URL (false)
     * @param string|null $caption Подпись к аудио (опционально)
     * @return void
     */
    public function sendAudio(?string $fileUrl, bool $isFile = true, ?string $caption = null): void
    {
        try {
            if (!$fileUrl) {
                return;
            }

            $path = $isFile ? Yii::getAlias('@webRoot') . $fileUrl : $fileUrl;

            if ($isFile && !is_readable($path)) {
                ErrorLogHelper::logBotInfo('Ошибка(файл не читаемый) при отправке аудио в Telegram пользователю ' . $this->telegramUserDto->getUserId());
                return;
            }

            $audio = $isFile ? InputFile::create($path) : $fileUrl;

            $parameters = [
                'chat_id' => $this->telegramUserDto->getUserId(),
                'audio' => $audio,
            ];

            if ($caption) {
                $parameters['caption'] = $caption;
                $parameters['parse_mode'] = 'HTML';
            }

            $this->telegram->sendAudio($parameters);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage() . $e->getTraceAsString(),
                'Ошибка при отправке аудио в Telegram пользователю ' . $this->telegramUserDto->getUserId()
            );
            ErrorLogHelper::logBotInfo($fileUrl);
        }
    }

    /**
     * Отправляет видео файл пользователю в Telegram
     *
     * @param string|null $fileUrl Путь к видео файлу или URL
     * @param bool $isFile Указывает, является ли $fileUrl локальным путем (true) или внешним URL (false)
     * @param string|null $caption Подпись к видео (опционально)
     * @return void
     */
    public function sendVideo(?string $fileUrl, bool $isFile = true, ?string $caption = null): void
    {
        try {
            if (!$fileUrl) {
                return;
            }

            $path = $isFile ? Yii::getAlias('@webRoot') . $fileUrl : $fileUrl;

            if ($isFile && !is_readable($path)) {
                ErrorLogHelper::logBotInfo('Ошибка(файл не читаемый) при отправке видео в Telegram пользователю ' . $this->telegramUserDto->getUserId());
                return;
            }

            $video = $isFile ? InputFile::create($path) : $fileUrl;

            $parameters = [
                'chat_id' => $this->telegramUserDto->getUserId(),
                'video' => $video,
            ];

            if ($caption) {
                $parameters['caption'] = $caption;
                $parameters['parse_mode'] = 'HTML';
            }

            $this->telegram->sendVideo($parameters);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage() . $e->getTraceAsString(),
                'Ошибка при отправке видео в Telegram пользователю ' . $this->telegramUserDto->getUserId()
            );
            ErrorLogHelper::logBotInfo($fileUrl);
        }
    }

    /**
     * Отправляет видео-сообщение (video note), видео в кружочке пользователю в Telegram
     *
     * @param string|null $fileUrl Путь к видео файлу или URL
     * @param bool $isFile Указывает, является ли $fileUrl локальным путем (true) или внешним URL (false)
     * @param int|null $length Диаметр видео-сообщения в пикселях (опционально)
     * @param int|null $duration Длительность видео-сообщения в секундах (опционально)
     * @return void
     */
    public function sendVideoNote(?string $fileUrl, bool $isFile = true, ?int $length = null, ?int $duration = null): void
    {
        try {
            if (!$fileUrl) {
                return;
            }

            $path = $isFile ? Yii::getAlias('@webRoot') . $fileUrl : $fileUrl;

            if ($isFile && !is_readable($path)) {
                ErrorLogHelper::logBotInfo('Ошибка(файл не читаемый) при отправке видео-сообщения в Telegram пользователю ' . $this->telegramUserDto->getUserId());
                return;
            }

            $videoNote = $isFile ? InputFile::create($path) : $fileUrl;

            $parameters = [
                'chat_id' => $this->telegramUserDto->getUserId(),
                'video_note' => $videoNote,
                'length' => $length ?? 640
            ];

            if ($duration !== null) {
                $parameters['duration'] = $duration;
            }

            $this->telegram->sendVideoNote($parameters);
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo(
                $e->getMessage() . $e->getTraceAsString(),
                'Ошибка при отправке видео-заметки в Telegram пользователю ' . $this->telegramUserDto->getUserId()
            );
            ErrorLogHelper::logBotInfo($fileUrl);
        }
    }
}