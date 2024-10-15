<?php

declare(strict_types=1);

namespace App\Services\StoredConfig\Interfaces;

use App\Services\StoredConfig\Dto\ConfigDtoCollection;

/**
 * Interface ConfigStorageInterface
 *
 * Интерфейс для хранения конфигурации.
 */
interface ConfigStorageInterface
{
    /**
     * Сохраняет конфигурацию.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param ConfigDtoCollection $config Объект конфигурации.
     */
    public function save(string $configId, ConfigDtoCollection $config): void;

    /**
     * Загружает конфигурацию.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param bool $clearCache Флаг загрузки конфигурации со сбросом кеша.
     * @return ConfigDtoCollection Объект конфигурации.
     */
    public function load(string $configId, bool $clearCache = false): ConfigDtoCollection;

    /**
     * Удаляет конфигурацию.
     *
     * @param string $configId Идентификатор конфигурации.
     */
    public function delete(string $configId): void;
}
