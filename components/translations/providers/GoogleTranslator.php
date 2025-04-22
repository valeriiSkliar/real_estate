<?php

namespace app\components\translations\providers;

use app\components\translations\interfaces\TranslatorInterface;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Translate\V2\TranslateClient;
use Yii;

class GoogleTranslator implements TranslatorInterface
{
    private $translateClient;
    private $language;
    private $targetLanguage;

    // Некоторые языки имеют другой код в Translate API
    private const LANGUAGE_MAP = [
        'ua' => 'uk',
    ];

    /**
     * @throws GoogleException
     */
    public function __construct()
    {
        $this->language = Yii::$app->language;

        $this->translateClient = new TranslateClient([
            'key' => Yii::$app->params['google_translation_api_key'] ?? null,
        ]);

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

    public function detectLanguage($text)
    {
        try {
            $result = $this->translateClient->detectLanguage($text);

            return $result['languageCode'] ?? $this->language;
        } catch (\Exception $e) {
            Yii::error('Google translation error(action=detectLanguage)' . $e->getMessage());
            Yii::error($text);
            return null;
        }


    }

    public function translate($text)
    {
        try {
            $detectedLanguage = $this->detectLanguage($text);

            if ($detectedLanguage != $this->targetLanguage) {
                $result = $this->translateClient->translate($text, [
                    'target' => $this->targetLanguage,
                ]);
                return $result['text'];
            }

            return $text;
        } catch (\Exception $e) {
            Yii::error('Google translation error(action=translateText)' . $e->getMessage());
            Yii::error($text);
            return null;
        }

    }

    public function translateBatch($texts)
    {
        try {
            $results = $this->translateClient->translateBatch($texts, [
                'target' => $this->targetLanguage,
            ]);

            $translatedTexts = [];
            foreach ($results as $result) {
                $translatedTexts[] = $result['text'];
            }

            return $translatedTexts;
        } catch (\Exception $e) {
            Yii::error('Google translation error(action=batchTranslate)' . $e->getMessage());
            Yii::error($texts);
            return null;
        }

    }

}