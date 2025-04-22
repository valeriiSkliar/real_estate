<?php

namespace app\helpers;

use Yii;

class LogInfoHelper
{
    public static function log($message, $category): void
    {
        try {
            Yii::info($message, $category);
            //self::flushLog();
        } catch (\Exception $e) {
            Yii::error($e->getMessage()
                . '/' . $e->getCode()
                . '/' . $e->getLine()
                . '/' . $e->getFile()
                . '/' . $message);
        }

    }

    public static function flushLog(): void
    {
        $log = Yii::getLogger();
        $log->flush(true);
    }

    public static function concatenateError($e): string
    {
        return $e->getMessage() . '/' . $e->getCode() . '/' . $e->getLine() . '/' . $e->getFile();
    }

    public static function fetchLogs($logFile, $category)
    {
        try {
            // Check if the log file exists
            if (!file_exists($logFile))
                return null;

            // Read the log file content
            $content = file_get_contents($logFile);

            // Split the log file content into individual lines
            $lines = explode("\n", $content);

            // Filter and retrieve only [info][$category] logs
            $filteredLogs = array_filter($lines, function ($line) use ($category) {
                return str_contains($line, '[info][' . $category . ']');
            });

            // Separate log messages with line breaks
            $filteredLogsString = implode("\n", $filteredLogs);

            return $filteredLogsString;
        } catch (\Exception $e) {
            self::log(self::concatenateError($e), $category);
            return false;
        }
    }

    public static function clearLog($logFile, $category)
    {
        try {
            if (!file_exists($logFile))
                return false;

            $fh = fopen($logFile, 'w');
            ftruncate($fh, 0);
            fclose($fh);

            return true;
        } catch (\Exception $e) {
            self::log(self::concatenateError($e), $category);
            return false;
        }
    }
}