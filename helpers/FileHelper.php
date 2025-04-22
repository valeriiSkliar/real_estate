<?php

namespace app\helpers;

class FileHelper
{
    public static function makeDirectory($folder): bool
    {
        if (!file_exists($folder)) {
            return mkdir($folder, 0777, true);
        }

        return true;
    }

    public static function createFile($filePath, $content): bool|int
    {
        return file_put_contents($filePath, $content);
    }

    public static function copy($sourcePath, $destinationPath): bool
    {
       return copy($sourcePath, $destinationPath);
    }
}