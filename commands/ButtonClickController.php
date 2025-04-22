<?php

namespace app\commands;

use app\models\ButtonClicks;
use yii\console\Controller;

class ButtonClickController extends Controller
{
    public function actionUpdateBotCounts(): void
    {
        $cache = \Yii::$app->cache;
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // Получаем все ключи кликов из массива, сохраненного в кэше
            $keys = $cache->get('button_click_keys') ?? [];
            $clickData = [];
            $currentDate = date('Y-m-d H:i:s');

            foreach ($keys as $name => $clickCount) {
                $buttonName = str_replace('button_click_count_', '', $name);

                // Собираем данные для вставки
                $clickData[] = [
                    'name' => $buttonName,
                    'counter' => $clickCount,
                    'date' => $currentDate,
                ];
            }

            // Вставляем данные в таблицу button_clicks
            if (!empty($clickData)) {
                $db->createCommand()->batchInsert('button_clicks', ['name', 'counter', 'date'], $clickData)->execute();
            }

            // Очищаем массив ключей кликов
            $cache->delete('button_click_keys');

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::error($e->getMessage());
            throw $e;
        }
    }
}