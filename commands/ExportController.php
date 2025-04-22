<?php

namespace app\commands;

use app\components\export\CsvExportService;
use app\components\export\ExportHandler;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ExportController extends Controller
{
    private ExportHandler $exportHandler;

    public function init(): void
    {
        parent::init();

        $basePath = Yii::getAlias('@webRoot/uploads/');

        // Инициализация сервиса экспорта CSV
        $csvExportService = new CsvExportService($basePath);

        // Инициализация обработчика экспорта
        $this->exportHandler = new ExportHandler($csvExportService);
    }

    /**
     * Экспортирует общие данные в CSV файл.
     *
     * @return int Код выхода
     */
    public function actionCommon(): int
    {
        try {
            $filePath = $this->exportHandler->exportCommonToFile();
            $this->stdout("Экспорт завершен. Файл сохранен по пути: {$filePath}\n");
            return ExitCode::OK;
        } catch (Exception $e) {
            $this->stderr("Ошибка при экспорте: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Экспортирует реферальные данные в CSV файл.
     *
     * @return int Код выхода
     */
    public function actionReferral(): int
    {
        try {
            $filePath = $this->exportHandler->exportReferralToFile();
            $this->stdout("Экспорт завершен. Файл сохранен по пути: {$filePath}\n");
            return ExitCode::OK;
        } catch (Exception $e) {
            $this->stderr("Ошибка при экспорте: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}