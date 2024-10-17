<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Dto;

use App\Services\BotService\Request\Handlers\Enums\HandlerUsageEnum;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

/**
 * Class BotConfigDto
 */
readonly class HandlerConfigDto implements ConfigDtoInterface
{
    public function __construct(
        public string $class,
        public HandlerUsageEnum $usage,
        public ConfigDtoInterface $config,
    ) {}
}