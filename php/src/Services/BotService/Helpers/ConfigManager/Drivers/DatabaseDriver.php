<?php

declare(strict_types=1);

namespace App\Services\BotService\Helpers\ConfigManager\Drivers;

use App\Services\BotService\Dto\ConfigDto as BotConfigDto;
use App\Services\BotService\Helpers\ConfigManager\Interfaces\DriverInterface;

/**
 * Class DatabaseDriver
 * Заготовка для драйвера, который будет получать конфигурации из базы данных.
 */
class DatabaseDriver implements DriverInterface
{
    /**
     * Возвращает список конфигов из базы данных.
     *
     * @return BotConfigDto[] Список конфигов.
     */
    public function getConfigs(): array
    {
        // Заготовка для получения конфигов из базы данных.
        return [];
    }
}
