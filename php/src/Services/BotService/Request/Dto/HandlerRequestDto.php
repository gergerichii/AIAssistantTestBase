<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Dto;

use App\Services\BotService\Request\Handlers\Enums\GptRolesEnum;

/**
 * Class HandlerRequestDto
 * DTO для представления запроса.
 */
readonly class HandlerRequestDto
{
    /**
     * @param string $message Сообщение запроса.
     * @param array<array{role: GptRolesEnum, text: string, data: ?array<string>}> $context Контекст запроса.
     */
    public function __construct(
        public string $message,
        public array $context = [],
        public bool $isFirstMessage = false,
    ) {
    }
}
