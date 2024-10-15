<?php

declare(strict_types=1);

namespace App\Services\StoredConfig;

use App\Services\StoredConfig\Helpers\ConfigAggregator;
use App\Services\StoredConfig\Interfaces\ConfigStorageInterface;
use App\Services\StoredConfig\Interfaces\ConfigProviderInterface;
use App\Services\StoredConfig\Dto\ConfigDtoCollection;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

/**
 * Class ConfigManager
 * Менеджер для управления конфигурацией модулей.
 */
class ConfigManager
{
    /**
     * Коллекция текущих конфигов.
     *
     * @var ConfigDtoCollection
     */
    private ConfigDtoCollection $configs;

    /**
     * Флаг, указывающий, изменялись ли конфиги.
     *
     * @var bool
     */
    private bool $isConfigChanged = false;

    /**
     * Конструктор класса ConfigManager.
     *
     * @param string $configId Имя конфигурации для хранения.
     * @param ConfigStorageInterface $storage Хранилище конфигурации.
     * @param ConfigAggregator $aggregator Хелпер для агрегации конфигов.
     */
    public function __construct(
        private readonly string $configId,
        private readonly ConfigStorageInterface $storage,
        private readonly ConfigAggregator $aggregator = new ConfigAggregator()
    ) {
        $this->configs = $this->storage->load(configId: $this->configId);
    }

    /**
     * Регистрирует модуль и добавляет его конфигурацию по умолчанию.
     *
     * @param ConfigProviderInterface $module Провайдер конфигурации для модуля.
     */
    public function registerModule(ConfigProviderInterface $module): void
    {
        $moduleIdentifier = $module->getModuleIdentifier();
        $moduleConfig = $module->getConfig();

        $this->addConfigIfNotExists($moduleIdentifier, $moduleConfig);

        $module->setConfig(newConfig: $this->getModuleConfig(moduleIdentifier: $module->getModuleIdentifier()));
    }

    /**
     * Возвращает конфигурацию для конкретного модуля.
     *
     * @param string $moduleIdentifier Уникальный идентификатор модуля.
     * @return ConfigDtoInterface|null Возвращает конфиг модуля или null, если не найден.
     */
    public function getModuleConfig(string $moduleIdentifier): ?ConfigDtoInterface
    {
        if ($this->configs->offsetExists($moduleIdentifier)) {
            return $this->configs->offsetGet($moduleIdentifier);
        }

        return null;
    }

    /**
     * Возвращает текущую коллекцию конфигов.
     *
     * @return ConfigDtoCollection Текущая коллекция конфигов.
     */
    public function getConfigs(): ConfigDtoCollection
    {
        return $this->configs;
    }

    /**
     * Обновляет конфигурацию для конкретного модуля и устанавливает флаг изменений.
     *
     * @param ConfigProviderInterface $module Провайдер конфигурации для модуля.
     */
    public function resetModuleConfig(ConfigProviderInterface $module): void
    {
        $moduleIdentifier = $module->getModuleIdentifier();
        $moduleConfig = $module->getConfig();

        $this->updateConfigInternal($moduleIdentifier, $moduleConfig);
    }

    /**
     * Регистрирует конфигурацию.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param ConfigDtoInterface $config Конфигурация.
     */
    public function registerConfig(string $configId, ConfigDtoInterface $config): ConfigDtoInterface
    {
        $this->addConfigIfNotExists($configId, $config);

        return $this->getConfigById($configId);
    }

    /**
     * Обновляет конфигурацию.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param ConfigDtoInterface $config Конфигурация.
     */
    public function updateConfig(string $configId, ConfigDtoInterface $config): void
    {
        $this->updateConfigInternal($configId, $config);
    }

    /**
     * Возвращает конфигурацию по идентификатору.
     *
     * @param string $configId Идентификатор конфигурации.
     * @return ConfigDtoInterface|null Конфигурация или null, если не найдена.
     */
    public function getConfigById(string $configId): ?ConfigDtoInterface
    {
        if ($this->configs->offsetExists($configId)) {
            return $this->configs->offsetGet($configId);
        }

        return null;
    }

    /**
     * Удаляет конфигурацию полностью.
     */
    public function reset(): void
    {
            $this->storage->delete($this->configId);
    }

    /**
     * При уничтожении экземпляра ConfigManager проверяем, нужно ли сохранить конфиги.
     */
    public function __destruct()
    {
        if ($this->isConfigChanged) {
            $this->saveConfigs();
        }
    }

    /**
     * Сохраняет текущие конфиги в хранилище.
     */
    private function saveConfigs(): void
    {
        $this->storage->save(configId: $this->configId, config: $this->configs);
    }

    /**
     * Добавляет конфигурацию, если она не существует.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param ConfigDtoInterface $config Конфигурация.
     */
    private function addConfigIfNotExists(string $configId, ConfigDtoInterface $config): void
    {
        if (!$this->configs->offsetExists($configId)) {
            $this->updateConfigInternal($configId, $config);
        }
    }

    /**
     * Обновляет конфигурацию и устанавливает флаг изменений.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param ConfigDtoInterface $config Конфигурация.
     */
    private function updateConfigInternal(string $configId, ConfigDtoInterface $config): void
    {
        $this->configs = $this->aggregator->aggregate(
            configDtos: [$configId => $config],
            existingCollection: $this->configs
        );
        $this->isConfigChanged = true;
    }
}
