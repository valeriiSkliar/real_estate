<?php

namespace app\commands;

use app\components\services\translations\TranslationService;
use app\helpers\ErrorLogHelper;
use app\helpers\FileHelper;
use app\models\Languages;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class FrontendTranslationController extends Controller
{
    private string $frontendOriginDir;
    private string $frontendOriginDirAuth;
    private string $frontendRemoteDir;
    private string $frontendRemoteDirAuth;
    private string $backendDir;

    public function init(): void
    {
        parent::init();

        $this->frontendOriginDir = getenv('FRONTEND_TRANSLATION_SOURCE_DIR');
        $this->frontendOriginDirAuth = getenv('FRONTEND_TRANSLATION_AUTH_DIR');
        $this->frontendRemoteDir = getenv('FRONTEND_REMOTE_TRANSLATION_SOURCE_DIR');
        $this->frontendRemoteDirAuth = getenv('FRONTEND_REMOTE_TRANSLATION_AUTH_DIR');
        $this->backendDir = Yii::getAlias('@webRoot/uploads/translation/');;
    }

    /**
     * Копирует файл из указанного пути в директорию переводов.
     *
     * @return int
     */
    public function actionCopyOriginal(): int
    {
        ErrorLogHelper::logTranslationInfo("Запущена команда копирования оригиналов");

        $sourceDir = $this->frontendOriginDir;
        $sourceDirAuth = $this->frontendOriginDirAuth;
        $destinationDir = $this->backendDir;
        $destinationAuthDir = $this->backendDir . 'auth/';

        ErrorLogHelper::logTranslationInfo("Копируем файлы авторизации...");
        // Копирование файлов авторизации
        $authFinished = $this->copyFrontendFiles($sourceDirAuth, $destinationAuthDir);

        ErrorLogHelper::logTranslationInfo("Копируем файлы сайта...");
        // Копирование остальных файлов
        $siteFinished = $this->copyFrontendFiles($sourceDir, $destinationDir);

        return ExitCode::OK;
    }

    public function actionCopyTranslations(): int
    {
        ErrorLogHelper::logTranslationInfo("Запущена команда копирования переводов на сайт");

        $languages = Languages::getActiveLanguages(); // Список активных языков
        $successCount = 0;
        $totalCount = count($languages);
        $activeFiles = []; // Список файлов, которые должны остаться

        $backDir = $this->backendDir;
        $backAuthDir = $this->backendDir . 'auth/';

        foreach ($languages as $language) {
            $fileName = $language->slug . '.json';
            $destinationPath = $this->frontendOriginDir . $fileName;
            $destinationAuthPath = $this->frontendOriginDirAuth . $fileName;
            $sourcePath = $backDir . $fileName;
            $sourceAuthPath = $backAuthDir . $fileName;

            // Добавляем файл в список активных
            $activeFiles[] = $fileName;

            // Проверка существования исходного файла
            if (!$this->isFileExist($sourcePath)) {
                ErrorLogHelper::logTranslationInfo("НЕТ такого файла в исходной директории: $sourcePath");

                continue;
            }

            if (!$this->isFileExist($sourceAuthPath)) {
                ErrorLogHelper::logTranslationInfo("НЕТ такого файла в исходной директории: $sourceAuthPath");

                continue;
            }

            // Копирование файла сайта
            if ($this->copy($sourcePath, $destinationPath)) {
                ++$successCount;
            }

            // Копирование файла авторизации
            $this->copy($sourceAuthPath, $destinationAuthPath);
        }

        // Удаление файлов, которые не входят в список активных(пока только для сайта используем)
        $deletedCount = $this->deleteInactiveFiles($this->frontendOriginDir, $activeFiles);

        ErrorLogHelper::logTranslationInfo("Скопировано $successCount из $totalCount. Удалено $deletedCount файлов.\n");
        $this->stderr("Скопировано $successCount из $totalCount. Удалено $deletedCount файлов.\n");

        return ExitCode::OK;
    }

    public function actionCopyFromTestToProd(): int
    {
        ErrorLogHelper::logTranslationInfo("Запущена команда копирования переводов с тестового сайта на продакшен");

        $sourceDir = $this->frontendOriginDir;
        $sourceDirAuth = $this->frontendOriginDirAuth;
        $destinationDir = $this->frontendRemoteDir;
        $destinationDirAuth = $this->frontendRemoteDirAuth;

        ErrorLogHelper::logTranslationInfo("Копируем файлы авторизации...");
        // Копирование файлов авторизации
        $authFinished = $this->copyFrontendFiles($sourceDirAuth, $destinationDirAuth);

        ErrorLogHelper::logTranslationInfo("Копируем файлы сайта...");
        // Копирование остальных файлов
        $siteFinished = $this->copyFrontendFiles($sourceDir, $destinationDir);

        return ExitCode::OK;
    }

    private function isFileExist($sourcePath): bool
    {
        try {
            if (!file_exists($sourcePath)) {
                ErrorLogHelper::logTranslationInfo("Файл $sourcePath не найден.\n");
                $this->stderr("Файл $sourcePath не найден.\n");

                return false;
            }

            return true;
        } catch (Throwable $e) {
            ErrorLogHelper::logTranslationInfo($e->getMessage(),"Ошибка при поиске $sourcePath .\n");

            return false;
        }

    }

    private function copy($sourcePath, $destinationPath): bool
    {
        try {
            if (FileHelper::copy($sourcePath, $destinationPath)) {
                ErrorLogHelper::logTranslationInfo("Файл $sourcePath успешно скопирован в $destinationPath.\n");
                $this->stdout("Файл $sourcePath успешно скопирован в $destinationPath.\n");

                return true;
            }

            ErrorLogHelper::logTranslationInfo("Не удалось скопировать $sourcePath в $destinationPath.\n");
            $this->stdout("Не удалось скопировать $sourcePath в $destinationPath.\n");

            return false;
        } catch (Throwable $e) {
            ErrorLogHelper::logTranslationInfo($e->getMessage(), "Ошибка при копировании $sourcePath .\n");

            return false;
        }
    }

    private function deleteInactiveFiles(string $directory, array $activeFiles): int
    {
        $deletedCount = 0;

        // Получаем список всех JSON-файлов в директории
        $files = glob($directory . '*.json');

        foreach ($files as $file) {
            $fileName = basename($file); // Имя файла без пути

            // Если файл не в списке активных, удаляем его
            if (!in_array($fileName, $activeFiles)) {
                if (unlink($file)) {
                    ++$deletedCount;
                } else {
                    // Можно логировать ошибку удаления
                    ErrorLogHelper::logTranslationInfo("Не удалось удалить файл: $file.\n");
                }
            }
        }

        ErrorLogHelper::logTranslationInfo("Удалено $deletedCount неиспользуемых файлов переводов на фронте.\n");

        return $deletedCount;
    }

    private function copyFrontendFiles($sourceDir, $destinationDir): int
    {
        try {
            if (!FileHelper::makeDirectory($destinationDir)) {
                ErrorLogHelper::logTranslationInfo("Не удалось создать директорию $destinationDir.");

                return ExitCode::IOERR;
            }

            $files = glob($sourceDir . '*.json');

            foreach ($files as $file) {
                $fileName = basename($file);
                $sourcePath = $file;
                $destinationPath = $destinationDir . $fileName;

                if (!$this->copy($sourcePath, $destinationPath)) {
                    ErrorLogHelper::logTranslationInfo("Не удалось скопировать файл $fileName.");

                    continue;
                }
            }

            ErrorLogHelper::logTranslationInfo("Завершено копирование переводов из $sourceDir в $destinationDir.");
            $this->stdout("Все переводы успешно скопированы.\n");

            return ExitCode::OK;
        } catch (Throwable $e) {
            ErrorLogHelper::logTranslationInfo("Ошибка при копировании переводов: " . $e->getMessage());
            $this->stderr("Ошибка при копировании переводов: " . $e->getMessage() . "\n");

            return ExitCode::IOERR;
        }
    }
}