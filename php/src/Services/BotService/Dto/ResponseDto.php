<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

use App\Services\BotService\Enum\ResponseStatusEnum;

/**
 * Class ResponseDto
 * DTO для представления ответа.
 */
readonly class ResponseDto
{
    /**
     * @param string $result Результат обработки запроса.
     * @param ResponseStatusEnum $status Статус ответа.
     */
    public function __construct(
        public string $result,
        public ResponseStatusEnum $status,
    ) {
    }
}