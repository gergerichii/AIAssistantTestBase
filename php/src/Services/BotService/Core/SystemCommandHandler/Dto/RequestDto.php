<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler\Dto;

/**
 * Class RequestDto
 * DTO для представления запроса системной команды.
 */
readonly class RequestDto
{
    /**
     * @param string $command Системная команда.
     * @param array<string, mixed> $parameters Параметры команды.
     */
    public function __construct(
        public string $command,
        public array $parameters = [],
    ) {}
}
