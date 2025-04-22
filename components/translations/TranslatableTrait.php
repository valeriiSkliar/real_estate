<?php

namespace app\components\translations;

use yii\db\ActiveQuery;

trait TranslatableTrait
{
    /**
     * Получает переведенные сущности
     *
     * @return ActiveQuery
     */
    public function getTranslatedEntities(): ActiveQuery
    {
        return $this->hasMany(static::class, ['slug' => 'slug'])
            ->andFilterWhere(['<>', 'id', $this->id]);
    }
}