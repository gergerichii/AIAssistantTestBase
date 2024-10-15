<?php

declare(strict_types=1);

namespace App\Services\StoredConfig\Helpers;

use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;
use App\Services\StoredConfig\Dto\ConfigDtoCollection;

/**
 * Class ConfigAggregator
 * Класс для агрегации конфигураций из различных источников.
 */
class ConfigAggregator
{
    /**
     * Объединяет массив DTO конфигураций в коллекцию.
     *
     * @param ConfigDtoInterface[] $configDtos Массив объектов конфигураций от разных модулей.
     * @param ConfigDtoCollection|null $existingCollection Существующая коллекция конфигураций.
     * @return ConfigDtoCollection Объединенная коллекция конфигураций.
     */
    public function aggregate(
        array $configDtos,
        ?ConfigDtoCollection $existingCollection = null
    ): ConfigDtoCollection {
        foreach ($configDtos as $configDto) {
            if (!$configDto instanceof ConfigDtoInterface) {
                throw new \InvalidArgumentException(
                    'Все элементы должны реализовывать ' . ConfigDtoInterface::class
                );
            }
        }

        $allItems = $existingCollection !== null ? $existingCollection->toArray() : [];
        $allItems = array_merge($allItems, $configDtos);

        return new ConfigDtoCollection($allItems);
    }
}
