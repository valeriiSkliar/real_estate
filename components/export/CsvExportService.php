<?php

namespace app\components\export;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\web\Response;

class CsvExportService extends Component
{
    private string $basePath;

    public function __construct(string $basePath, $config = [])
    {
        $this->basePath = rtrim($basePath, '/') . '/';
        parent::__construct($config);
    }

    /**
     * Универсальный метод для подготовки данных и экспорта CSV.
     *
     * @param string $filename Имя файла
     * @param array $headers Заголовки CSV
     * @param array $dataProvider Массив данных
     * @param bool $toString Определяет, экспортировать ли как строку или в файл
     * @return string Путь к файлу или CSV строка
     * @throws Exception
     */
    public function prepareAndExport(string $filename, array $headers, array $dataProvider, bool $toString = false): string
    {
        $dataRows = $dataProvider;

        if ($toString) {
            return $this->exportToString($headers, $dataRows);
        } else {
            return $this->exportToFile($filename, $headers, $dataRows);
        }
    }

    /**
     * Экспортирует данные в CSV файл.
     *
     * @param string $filename Имя файла
     * @param array $headers Заголовки CSV
     * @param iterable $dataRows Данные для записи
     * @return string Путь к сохранённому файлу
     * @throws Exception Если не удалось открыть файл
     */
    public function exportToFile(string $filename, array $headers, iterable $dataRows): string
    {
        $filePath = $this->basePath . $filename;

        $file = fopen($filePath, 'w');
        if ($file === false) {
            throw new Exception("Не удалось открыть файл для записи: {$filePath}");
        }

        fputcsv($file, $headers, ",");

        foreach ($dataRows as $row) {
            fputcsv($file, $row, ",");
        }

        fclose($file);

        return $filePath;
    }

    /**
     * Экспортирует данные в CSV строку.
     *
     * @param array $headers Заголовки CSV
     * @param iterable $dataRows Данные для записи
     * @return string CSV контент
     * @throws Exception Если не удалось создать CSV
     */
    public function exportToString(array $headers, iterable $dataRows): string
    {
        $fh = fopen('php://temp', 'w+');
        if ($fh === false) {
            throw new Exception("Cannot open temporary memory for CSV export.");
        }

        fputcsv($fh, $headers, ",");

        foreach ($dataRows as $row) {
            fputcsv($fh, $row, ",");
        }

        rewind($fh);
        $content = stream_get_contents($fh);
        fclose($fh);

        if ($content === false) {
            throw new Exception("Failed to get CSV content from memory.");
        }

        return $content;
    }

    /**
     * Отправляет CSV файл в ответ HTTP-запроса.
     *
     * @param string $filename Имя файла
     * @param string $content CSV контент
     * @return Response
     */
    public function sendCsv(string $content, string $filename): Response
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        $response->content = $content;

        return $response;
    }
}