<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Dto;

use App\Services\IntelligentBotService\Enums\ResponseStatusEnum;

/**
 * Class ResponseDto
 * DTO для представления ответа.
 */
readonly class ResponseDto
{
    /**
     * @param string $result Результат обработки запроса.
     * @param string[] $context Исходное сообщение от пользователя и контекст.
     * @param ResponseStatusEnum $status Статус ответа.
     */
    public function __construct(
        public string $result,
        public array $context,
        public ResponseStatusEnum $status
    ) {
    }
}
