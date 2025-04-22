<?php

namespace app\components\services;

use Yii;
use yii\db\Exception;

class RecordsTransferService
{
    public $existedLanguage;
    public $newLanguage;

    public $table;

    public function __construct($existedLanguage, $newLanguage, $table)
    {
        $this->existedLanguage = $existedLanguage;
        $this->newLanguage = $newLanguage;
        $this->table = $table;
    }

    /**
     * @throws Exception
     * Копирует все записи с существующим языком с заменой на новый язык,
     * исключая записи, где slug содержит 'tariff' или 'language', если таблица buttons
     */
    public function copyRecords()
    {
        // Получение метаданных таблицы
        $schema = Yii::$app->db->schema;
        $tableSchema = $schema->getTableSchema($this->table);

        // Получаем все имена колонок, исключая первичный ключ (например, 'id') и 'language'
        $columns = $tableSchema->columnNames;
        $columns = array_diff($columns, [$tableSchema->primaryKey[0], 'language']);

        // Формируем строку имен колонок и строку имен колонок для SELECT части
        $columnsString = implode(', ', $columns);
        $selectColumnsString = implode(', ', array_map(function ($column) {
            return $column === 'name' ? "CONCAT('Копия-', name)" : $column;
        }, $columns));

        // Добавляем колонку language отдельно
        $columnsString .= ', language';
        $selectColumnsString .= ", :newLanguage";

        // Основная часть SQL-запроса
        $sql = "INSERT INTO {$this->table} ($columnsString)
            SELECT $selectColumnsString
            FROM {$this->table}
            WHERE language = :existedLanguage";

        // Добавляем условие на slug только если таблица buttons
        if ($this->table === 'buttons') {
            $sql .= " AND slug NOT LIKE 'tariff-%'
                  AND slug NOT LIKE 'language-%'";
        }

        // Выполнение запроса
        Yii::$app->db->createCommand($sql, [
            ':newLanguage' => $this->newLanguage,
            ':existedLanguage' => $this->existedLanguage,
        ])->execute();
    }


    public function copyRecordById($id)
    {
        // Перечисляем все поля, которые нужно копировать
        $columns = [
            'name',
            'photo_url',
            'description',
            'created_at',
            'keywords',
            'type',
            'meta_title',
            'meta_description'
        ];

        // Формируем строку имен колонок для INSERT и SELECT частей
        $columnsString = implode(', ', $columns);
        $selectColumnsString = implode(', ', array_map(function ($column) {
            return $column === 'name' ? "CONCAT('Копия-', name)" : $column;
        }, $columns));

        // Добавляем колонку language отдельно
        $columnsString .= ', language';
        $selectColumnsString .= ', :newLanguage';

        // SQL-запрос
        $sql = "INSERT INTO {$this->table} ($columnsString)
            SELECT $selectColumnsString
            FROM {$this->table}
            WHERE id = :id";

        // Выполнение запроса
        Yii::$app->db->createCommand($sql, [
            ':newLanguage' => $this->newLanguage,
            ':id' => $id,
        ])->execute();
    }

}