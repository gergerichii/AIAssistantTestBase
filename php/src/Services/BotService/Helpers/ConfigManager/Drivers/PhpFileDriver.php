<?php

declare(strict_types=1);

namespace App\Services\BotService\Helpers\ConfigManager\Drivers;

use App\Services\BotService\Dto\ConfigDto;
use App\Services\BotService\Helpers\ConfigManager\Interfaces\DriverInterface;

/**
 * Class FileDriver
 * Драйвер для получения конфигураций из файлов.
 */
readonly class PhpFileDriver implements DriverInterface
{
    /**
     * @param string $path Путь к файлам конфигурации.
     */
    public function __construct(
        public string $path,
    ) {
    }

    /**
     * Возвращает список конфигов из файлов.
     *
     * @return ConfigDto[] Список конфигов.
     */
    public function getConfigs(): array
    {
        $configFiles = glob($this->path . '/*.php');
        $configs = [];

        foreach ($configFiles as $file) {
            /** @var ConfigDto $config */
            $config = require $file;
            $configs[$config->id] = $config;
        }

        return $configs;
    }
}
