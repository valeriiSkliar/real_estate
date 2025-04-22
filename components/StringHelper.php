<?php

namespace app\components;

class StringHelper
{
    public static function transformToTelegramValidText($html): string
    {
        // Заменяем запрещенные теги на поддерживаемые или удаляем их
        $replacements = [
            '<p>' => "\n",
            '</p>' => '',
            '<h1' => '<b',
            '</h1>' => '</b>' . "\n",
            '<h2' => '<b',
            '</h2>' => '</b>' . "\n",
            '<h3' => '<b',
            '</h3>' => '</b>' . "\n",
            '<h4' => '<b',
            '</h4>' => '</b>' . "\n",
            '<h5' => '<b',
            '</h5>' => '</b>' . "\n",
            '<h6' => '<b',
            '</h6>' => '</b>' . "\n",
            '<strong' => '<b',
            '</strong>' => '</b>',
            '<em' => '<i',
            '</em>' => '</i>',
            '<strike' => '<s',
            '</strike>' => '</s>',
            '<del' => '<s',
            '</del>' => '</s>',
            '<br>' => "\n",
            '<br />' => "\n",
            '<br/>' => "\n",
            '&nbsp;' => ' ',
            '<ul' => "<div",
            '</ul>' => "</div>",
            '<ol>' => "\n",
            '</ol>' => "\n",
            '<li>' => '• ',
            '</li>' => "",
            '<pre>' => "",
            '</pre>' => "",
        ];

        // Удаляем теги <span> с атрибутами
        $html = preg_replace('/<span[^>]*>|<\/span>/', '', $html);

        // Заменяем <p> с любыми атрибутами на переход строки
        $html = preg_replace('/<p[^>]*>/', "\n", $html);

        // Выполняем замену тегов
        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        // Удаление запрещенных тегов
        return strip_tags($html, '<b><i><u><s><code><pre><a>');
    }

    public static function countTelegramTextLength(string $string): string
    {
        $preparedTelegramHtml = self::transformToTelegramValidText($string);

        return mb_strlen($preparedTelegramHtml);
    }

    /**
     * Обрезает HTML строку, сохраняя корректность тегов.
     *
     * @param string $html  HTML строка
     * @param int    $limit Ограничение по количеству символов
     *
     * @return string Обрезанная HTML строка
     */
    public static function truncateHtmlSimple(string $html, int $limit): string
    {
        $textLength = 0;
        $result = '';
        $isInsideTag = false;
        $tagStack = [];

        // Разбиваем строку на символы
        for ($i = 0, $iMax = mb_strlen($html, 'UTF-8'); $i < $iMax; $i++) {
            $char = mb_substr($html, $i, 1, 'UTF-8');

            if ($char === '<') {
                $isInsideTag = true;
                $result .= $char;
            } elseif ($char === '>') {
                $isInsideTag = false;
                $result .= $char;

                // Закрывающий тег (например, </p>)
                if (str_contains($result, '</')) {
                    array_pop($tagStack);
                }
                // Открывающий тег (например, <p>)
                elseif (preg_match('/<(\w+)/', $result, $matches)) {
                    $tagStack[] = $matches[1];
                }
            } elseif (!$isInsideTag && $textLength < $limit) {
                $result .= $char;
                $textLength++;
            } elseif ($isInsideTag) {
                $result .= $char;
            }

            // Прерываем, если достигли лимита по символам
            if ($textLength >= $limit && !$isInsideTag) {
                break;
            }
        }

        // Закрываем все открытые теги
        while (!empty($tagStack)) {
            $result .= '</' . array_pop($tagStack) . '>';
        }

        return $result;
    }
}