<?php

namespace app\helpers;

use DOMDocument;
use HTMLPurifier;
use HTMLPurifier_Config;
use Yii;

class HtmlHelper
{
    public static function truncateSimpleHtml($html, $length = 100): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', Yii::getAlias('@app/runtime/cache'));
        $purifier = new HTMLPurifier($config);

        // Очистка HTML
        $cleanHtml = $purifier->purify($html);

        // Обрезка строки до нужной длины
        $truncated = mb_substr($cleanHtml, 0, $length);

        // Закрытие всех незакрытых тегов
        return $purifier->purify($truncated);
    }

    public static function truncateComplexHtml($html, $length = 100): string
    {
        // Создаем конфигурацию HTMLPurifier
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', Yii::getAlias('@app/runtime/cache'));
        $purifier = new HTMLPurifier($config);

        // Очищаем HTML
        $cleanHtml = $purifier->purify($html);

        // Создаем новый DOMDocument
        $doc = new DOMDocument();
        // Загружаем HTML
        @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $cleanHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Получаем все текстовые узлы
        $textNodes = [];
        self::getTextNodes($doc, $textNodes);

        $currentLength = 0;
        $truncatePos = -1;

        // Идем по узлам, считаем длину текста и определяем позицию обрезки
        foreach ($textNodes as $textNode) {
            $currentLength += mb_strlen($textNode->nodeValue);
            if ($currentLength >= $length) {
                $truncatePos = $length - ($currentLength - mb_strlen($textNode->nodeValue));
                $textNode->nodeValue = mb_substr($textNode->nodeValue, 0, $truncatePos);
                break;
            }
        }

        // Преобразуем обратно в HTML
        $truncatedHtml = $doc->saveHTML();

        // Очистка от лишних тегов
        $truncatedHtml = $purifier->purify($truncatedHtml);

        return $truncatedHtml;
    }

    private static function getTextNodes($node, &$textNodes)
    {
        if ($node->nodeType == XML_TEXT_NODE) {
            $textNodes[] = $node;
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                self::getTextNodes($childNode, $textNodes);
            }
        }
    }

}