<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Dto;

use App\Services\BotService\SystemCommand\Handlers\Enum\HandlerUsageEnum;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

/**
 * Class SystemCommandHandlerConfigDto
 * DTO для конфигурации обработчиков системных команд.
 */
readonly class SystemCommandHandlerConfigDto implements ConfigDtoInterface
{
    /**
     * @param string $class Класс обработчика.
     * @param HandlerUsageEnum $usage Признак использования обработчика.
     * @param ConfigDtoInterface $config Конфигурация обработчика.
     */
    public function __construct(
        public string $class,
        public HandlerUsageEnum $usage,
        public ConfigDtoInterface $config,
    ) {}
}
