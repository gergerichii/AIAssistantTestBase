<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Dto;

use App\Services\IntelligentBotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;
use App\Services\StoredConfig\Interfaces\ConfigProviderInterface;

/**
 * Class BotConfigDto
 * DTO для конфигурации бота, содержащий список обработчиков.
 */
readonly class BotConfigDto implements ConfigDtoInterface
{
    /**
     * @param array<MessageHandlerInterface|ConfigProviderInterface> $handlers Массив обработчиков.
     */
    public function __construct(
        public array $handlers,
        public string $botId = self::class . '_' . 'ChatBot',
    ) {}
}