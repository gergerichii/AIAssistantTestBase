<?php

declare(strict_types=1);

namespace App\Services\BotService\Helpers\ConfigManager\Interfaces;

use App\Services\BotService\Dto\ConfigDto;

/**
 * Interface DriverInterface
 * Интерфейс для драйверов конфигурации.
 */
interface DriverInterface
{
    /**
     * Возвращает список конфигов.
     *
     * @return array<string, ConfigDto> Список конфигов, где ключ - айди конфига.
     */
    public function getConfigs(): array;
}
