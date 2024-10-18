<?php

declare(strict_types=1);

namespace App\Services\BotService\Helpers;

use App\Services\BotService\Dto\ConfigDto;
use App\Services\BotService\Helpers\ConfigManager\Interfaces\DriverInterface;
use RuntimeException;

/**
 * Class BotConfigManager
 * Менеджер конфигураций бота.
 */
class BotConfigManager
{
    /**
     * @var DriverInterface|null Драйвер хранения конфигурации.
     */
    private ?DriverInterface $driver = null;
    /**
     * @var ConfigDto[]
     */
    private ?array $configs = null;

    /**
     * Конструктор BotConfigManager.
     *
     * @param DriverInterface $driver Драйверы конфигурации.
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Возвращает список конфигов.
     *
     * @return ConfigDto[] Список конфигов.
     */
    public function getConfigs(bool $useCache = true): array
    {
        return $this->configs ??= $this->driver->getConfigs();
    }

    /**
     * Возвращает список имен конфигов.
     *
     * @param bool $useCache Флаг использования кеша.
     * @return array Массив, где ключ - айди конфига, а значение - его имя.
     */
    public function getConfigsNames(bool $useCache = true): array
    {
        $configs = $this->getConfigs($useCache);

        return array_map(
            static fn(ConfigDto $config): string => $config->name,
            $configs,
        );
    }

    /**
     * Возвращает конфиг по идентификатору.
     *
     * @param string $configId Идентификатор конфига.
     * @return ConfigDto Конфиг.
     * @throws RuntimeException Если конфиг не найден.
     */
    public function getConfigById(string $configId): ConfigDto
    {
        $configs = $this->getConfigs();

        if (!isset($configs[$configId])) {
            throw new RuntimeException("Config with ID {$configId} not found.");
        }

        return $configs[$configId];
    }
}
