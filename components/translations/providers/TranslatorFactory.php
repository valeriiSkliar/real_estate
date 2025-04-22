<?php

namespace app\components\translations\providers;

use app\components\translations\interfaces\TranslatorInterface;
use app\components\translations\providers\DeeplFreeTranslator;
use app\components\translations\providers\DeeplTranslator;
use app\components\translations\providers\GoogleTranslator;
use Google\Cloud\Core\Exception\GoogleException;
use Yii;

class TranslatorFactory
{
    /**
     * @throws GoogleException
     */
    public static function createTranslator() : TranslatorInterface
    {
        $translator = Yii::$app->cache->get('translator_provider') ?: Yii::$app->params['translator'] ?? 'google';

        return match ($translator) {
            'deepl' => new DeeplTranslator(),
            'deepl-free' => new DeeplFreeTranslator(),
            default => new GoogleTranslator(),
        };
    }
}