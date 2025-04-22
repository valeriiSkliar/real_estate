<?php

namespace app\components\services\bot;

use app\components\telegram\handlers\TelegramApiHandler;
use app\helpers\ErrorLogHelper;
use app\models\dto\TelegramUserDto;
use app\models\Languages;
use Exception;

class TelegramDataParseService
{
    public TelegramUserDto $userDto;

    /**
     * @throws Exception
     */
    public function __construct(private readonly TelegramApiHandler $telegram)
    {
        $this->userDto = $this->parseData();
    }

    public function getUserDto(): TelegramUserDto
    {
        return $this->userDto;
    }

    private function getLanguage($message): string
    {
        if (isset($message['from']['language_code'], $message['text']) && $message['text'] === '/start') {
            $language = Languages::findOne([
                'slug' => $message['from']['language_code'],
                'is_active' => Languages::STATUS_ACTIVE,
            ]);

            if ($language) {
                return $language->slug;
            }
        }

        return 'ru';
    }

    private function getText($text): string
    {
        return trim(strip_tags(stripslashes($text)));
    }

    /**
     * @throws Exception
     */
    private function parseData(): TelegramUserDto
    {
        $update = $this->telegram->getWebhookUpdate();
        $callbackQuery = $update->get('callback_query');
        $message = $update->get('message');
        $data = '';

        if ($callbackQuery) {
            $data = $callbackQuery['data'] ?? '';
            $message = $callbackQuery['message'] ?? '';
        } else {
            $message = json_decode($message, true);
        }

        if (isset($message['chat']['id'])) {
            return $this->processData($message, $data);
        }

        return new TelegramUserDto([]);
    }

    public function processData($message, $data): TelegramUserDto
    {
        $userId = preg_replace('/\D/', '', $message['chat']['id']);

        if ($data) {
            $firstName = $message['chat']['first_name'] ?? '';
            $lastName = $message['chat']['last_name'] ?? '';
            $username = $message['chat']['username'] ?? '';
            $phone = $message['contact']['phone_number'] ?? '';
        } else {
            $firstName = $message['from']['first_name'] ?? '';
            $lastName = $message['message']['from']['last_name'] ?? '';
            $username = $message['from']['username'] ?? '';
        }

        if (empty($phone)){
            $phone = $message['contact']['phone_number']?? null;
        }

        $text = ($phone) ? '/start' : ($message['text'] ?? '');
        $text = $this->getText($text);
        $language = $this->getLanguage($message);

        return new TelegramUserDto([
            'userId' => $userId,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'username' => $username,
            'phone' => $phone,
            'language' => $language,
            'text' => $text,
            'data' => $data,
        ]);
    }
}