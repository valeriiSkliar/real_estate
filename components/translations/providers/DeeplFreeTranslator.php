<?php

namespace app\components\translations\providers;

use app\components\translations\interfaces\TranslatorInterface;
use Yii;
use yii\httpclient\Client;

class DeeplFreeTranslator implements TranslatorInterface
{
    private $apiKey;
    private $client;
    private $baseUrl = 'https://api-free.deepl.com/v2/translate';
    private $targetLanguage;
    private $sourceLang;
    // Некоторые языки имеют другой код в Translate API
    private const LANGUAGE_MAP = [
        'ua' => 'uk',
    ];

    public function __construct()
    {
        $this->apiKey = Yii::$app->params['deepl_free_translation_api_key'] ?? null;
        $this->client = new Client();
    }

    public function setTargetLanguage($targetLanguage): void
    {
        if (isset(self::LANGUAGE_MAP[$targetLanguage])) {
            $this->targetLanguage = self::LANGUAGE_MAP[$targetLanguage];
        }else{
            $this->targetLanguage = $targetLanguage;
        }
    }

    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }

    public function translate($text)
    {
        try {
            $response = $this->send([$text]);

            if ($response->isOk) {
                $responseBody = $response->data;

                return $responseBody['translations'][0]['text'];
            }

            return null;
        } catch (\Exception $e) {
            Yii::error('Deepl translation error(action=translate)' . $e->getMessage());
            Yii::error($text);

            return null;
        }
    }

    public function translateBatch($texts)
    {
        try {
            $response = $this->send($texts);

            if ($response->isOk) {
                $translatedTexts = [];
                foreach ($response->data['translations'] as $translation) {
                    $translatedTexts[] = $translation['text'];
                }

                return $translatedTexts;
            }

            return $response;
        } catch (\Exception $e) {
            Yii::error('Deepl translation error(action=translateBatch)' . $e->getMessage());
            Yii::error($texts);
            return null;
        }
    }

    private function send($texts)
    {
        try {
            $data = [
                'text'        => $texts,
                'target_lang' => $this->targetLanguage,
                'source_lang' => $this->sourceLang,
            ];

            return $this->client->createRequest()
                ->setMethod('POST')
                ->setUrl($this->baseUrl)
                ->addHeaders(['Authorization' => 'DeepL-Auth-Key ' . $this->apiKey, 'Content-Type' => 'application/json'])
                ->setFormat(Client::FORMAT_JSON)
                ->setData($data)
                ->send();
        } catch (\Exception $e) {
            Yii::error('Deepl translation error(action=send)' . $e->getMessage());
            Yii::error($data);
            return null;
        }
    }

}