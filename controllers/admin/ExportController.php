<?php

namespace app\controllers\admin;

use app\components\export\CsvExportService;
use app\components\export\ExportHandler;
use Yii;
use yii\base\Exception;
use yii\web\Response;

class ExportController extends AdminController
{
    private ExportHandler $exportHandler;
    private CsvExportService $csvExportService;


    public function init(): void
    {
        parent::init();

        $basePath = Yii::getAlias('@webRoot/uploads/');

        // Инициализация сервиса экспорта CSV
        $this->csvExportService = new CsvExportService($basePath);

        // Инициализация обработчика экспорта
        $this->exportHandler = new ExportHandler($this->csvExportService);
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Экспортирует общие данные в CSV файл.
     *
     * @param string $filename Имя файла
     * @return Response Код выхода
     */
    public function actionCommon(string $filename = 'export.csv'): Response
    {
        try {
            $request = \Yii::$app->request;
            $dateFrom = $request->post('date-from');
            $dateTo = $request->post('date-to');
            $status = $request->post('status');
            $tariffId = $request->post('tariff_id');
            $filters = [];

            if ($dateFrom) {
                $filters[] = ['>=','payments.created_at', $dateFrom];
            }

            if ($dateTo) {
                $filters[] = ['<=','payments.created_at', $dateTo];
            }

            if ($status) {
                $filters[] = ['payments.status' => $status];
            }

            if ($tariffId) {
                $filters[] = ['payments.tariff_id' => $tariffId];
            }

            $csvContent = $this->exportHandler->exportCommonToString($filename, $filters);

            return $this->csvExportService->sendCsv($csvContent, $filename);
        } catch (Exception $e) {
            Yii::error('Ошибка при экспорте общих данных: '. $e->getMessage());

            Yii::$app->session->setFlash('error', 'Операция не удалась');
        }

        return $this->redirect(['index']);
    }
}