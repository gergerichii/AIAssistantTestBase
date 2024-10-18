<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

use App\Services\BotService\Core\RequestHandler\Dto\ConfigDto as RequestHandlerConfigDto;
use App\Services\BotService\Core\SystemCommandHandler\Dto\ConfigDto as SystemCommandHandlerConfigDto;

/**
 * Class ConfigDto
 * DTO для конфигурации бота.
 */
readonly class ConfigDto
{
    /**
     * @param string $id Идентификатор бота.
     * @param string $name Название бота.
     * @param RequestHandlerConfigDto[] $requestHandlers Обработчики запросов.
     * @param SystemCommandHandlerConfigDto[] $systemCommandHandlers Обработчики системных команд.
     */
    public function __construct(
        public string $id,
        public string $name,
        public array $requestHandlers,
        public array $systemCommandHandlers = [],
    ) {}
}
