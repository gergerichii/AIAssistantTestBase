<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Dto;

use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;
use App\Services\StoredConfigService\Interfaces\ConfigDtoInterface;

/**
 * Class ConfigDto
 */
readonly class ConfigDto implements ConfigDtoInterface
{
    public function __construct(
        public string $class,
        public HandlerUsageEnum $usage,
        public ConfigDtoInterface $config,
    ) {}
}