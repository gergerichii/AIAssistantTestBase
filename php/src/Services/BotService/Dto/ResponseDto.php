<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

use App\Services\BotService\Enums\ResponseStatusEnum;
use App\Services\BotService\Handlers\Enums\GptRolesEnum;

/**
 * Class ResponseDto
 * DTO для представления ответа.
 */
readonly class ResponseDto
{
    /**
     * @param string $result Результат обработки запроса.
     * @param array<array{role: GptRolesEnum, text: string, data: ?array<string>}> $context Исходное сообщение от пользователя и контекст.
     * @param ResponseStatusEnum $status Статус ответа.
     */
    public function __construct(
        public string $result,
        public array $context,
        public ResponseStatusEnum $status
    ) {
    }
}
