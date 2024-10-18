<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Dto;

use App\Services\BotService\Core\RequestHandler\Enums\HandlerResponseStatusEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;

/**
 * Class ResponseDto
 * DTO для представления ответа.
 */
readonly class ResponseDto
{
    /**
     * @param string $result Результат обработки запроса.
     * @param array<array{role: GptRolesEnum, text: string, data: ?array<string>}> $addToContext Исходное сообщение от пользователя и контекст.
     * @param HandlerResponseStatusEnum $status Статус ответа.
     * @param array<string> $data Дополнительные данные.
     */
    public function __construct(
        public string $result,
        public array $addToContext,
        public HandlerResponseStatusEnum $status,
        public array $data = []
    ) {
    }
}