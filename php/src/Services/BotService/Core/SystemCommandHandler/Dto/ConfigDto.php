<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler\Dto;

use App\Services\BotService\Core\SystemCommandHandler\Handlers\Enum\HandlerUsageEnum;
use App\Services\StoredConfigService\Interfaces\ConfigDtoInterface;

/**
 * Class ConfigDto
 * DTO для конфигурации обработчиков системных команд.
 */
readonly class ConfigDto implements ConfigDtoInterface
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
