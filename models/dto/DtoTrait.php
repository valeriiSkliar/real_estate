<?php

declare(strict_types=1);

namespace app\models\dto;

/**
 * Trait DtoTrait
 * Автоматическая инициализация свойств DTO на основе переданного массива.
 */
trait DtoTrait
{
    /**
     * Конструктор трейта.
     *
     * @param array $data Массив данных для инициализации.
     */
    public function __construct(array $data = [])
    {
        $this->initialize($data);
    }

    /**
     * Инициализирует свойства класса на основе массива данных.
     *
     * @param array $data Массив данных для инициализации.
     * @return void
     */
    protected function initialize(array $data): void
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $data)) {
                $property->setValue($this, $data[$name]);
            }
        }
    }
}