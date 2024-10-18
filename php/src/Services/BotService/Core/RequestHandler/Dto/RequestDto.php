<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Dto;

use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;

/**
 * Class RequestDto
 * DTO для представления запроса.
 */
readonly class RequestDto
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
