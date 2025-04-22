<?php

namespace app\helpers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use yii\web\Response;
class QRcodeGenerator
{
    public static function generate($text): string
    {
        try {
            $qrCode = new QrCode($text);
            $writer = new PngWriter();

            // Генерация QR кода
            $result = $writer->write($qrCode);

            // Возврат бинарных данных
            return $result->getString();
        } catch (\Exception $e) {
            return ' ';
        }
    }
}
