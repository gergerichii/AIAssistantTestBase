<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

use App\Services\BotService\Core\RequestHandler\Enums\HandlerResponseStatusEnum;

/**
 * Class ResponseDto
 * DTO для представления ответа.
 */
readonly class ResponseDto
{
    /**
     * @param string $result Результат обработки запроса.
     * @param HandlerResponseStatusEnum $status Статус ответа.
     */
    public function __construct(
        public string $result,
        public HandlerResponseStatusEnum $status,
    ) {
    }
}