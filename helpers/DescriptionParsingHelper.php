<?php

namespace app\helpers;

use app\enums\TopicTypes;
use app\models\Topics;
use yii\helpers\StringHelper;

class DescriptionParsingHelper
{
    public const BOT_OILS = [1,2,3,4];
    public const BOT_DISEASES = [3,4,5];

    /**
     * @param $topics
     * @param $description
     * Вернет массив с описанием, разбитым по темам.
     *
     * @return array|null
     */
    public static function getParsedDescription($topics,$description): array|null
    {
        $pattern = '/\[(.*?)\](.*?)(?=\[|$)/s';
        preg_match_all($pattern, $description, $matches, PREG_SET_ORDER);

        $parsed = [];
        foreach ($matches as $match) {
            $slug = $match[1];
            $content = $match[2];
            if (array_key_exists($slug, $topics)) {
                $parsed[$slug] = $content;
            }
        }
        return $parsed;
    }

    public static function arrayToString($descriptionParts): string
    {
        $description = '';
        foreach ($descriptionParts as $slug => $content) {
            $description .= "[$slug]$content";
        }

        return $description;
    }

    /**
     * Formats the parsed description with topic headings.
     *
     * @param array $topics
     * @param array $parsed
     * @param int   $limit
     *
     * @return string
     * Выставит оглавление для каждого раздела
     */
    public static function formatParsedDescription(array $topics, array $parsed, array $limit): string
    {
        $result = [];
        $counter = 1;
        $lastElement = end($parsed);
        $lastSlug = key($parsed);
        $lastAddedSlug = null;
        reset($parsed);  // Reset the array pointer to the beginning

        foreach ($parsed as $slug => $content) {
            if  ($content && in_array($counter, $limit, true)) {
                $header = "<p><br><h1>{$topics[$slug]['name']}</h1>";
                $result[] = $header . $content . "</p>";
                $lastAddedSlug = $slug;
            }

            $counter++;
        }

        if ($counter > 1 && $lastSlug !== $lastAddedSlug && $lastElement) {
            $header = "<p><br><h1>{$topics[$lastSlug]['name']}</h1>";
            $result[] = $header . $lastElement . "</p>";
        }

        return implode(' ', $result);
    }


    /**
     * @param $oil
     * Вернет короткое описание.
     * @param $language
     *
     * @return string
     */
    public static function getShortOilParsedDescription($oil, $language): string
    {
        $topics = Topics::getAllIndexedBySlug(TopicTypes::OIL->value, $language);
        $parsed = self::getParsedDescription($topics, $oil->description);

        return self::formatParsedDescription($topics, $parsed, self::BOT_OILS);
    }

    /**
     * @param $disease
     * Вернет короткое описание.
     * @param $language
     *
     * @return string
     */
    public static function getShortDiseaseParsedDescription($disease, $language): string
    {
        $topics = Topics::getAllIndexedBySlug(TopicTypes::DISEASE->value, $language);
        $parsed = self::getParsedDescription($topics, $disease->description);

        return self::formatParsedDescription($topics, $parsed, self::BOT_DISEASES);
    }

    public static function getBotKeys(array $description, array $allowedIds): array
    {
        $count = 1;
        $slugs = [];

        foreach ($description as $slug => $content) {
            if (in_array($count, $allowedIds, true)) {
                $slugs[] = $slug;
            }

            $count++;
        }

        return $slugs;
    }
}