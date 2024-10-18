<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

/**
 * Class RequestDto
 * DTO для представления запроса.
 */
readonly class RequestDto
{
    /**
     * @param string $message Сообщение запроса.
     * @param bool $isFirstMessage
     * @param bool $isClearContext
     */
    public function __construct(
        public string $message,
        public bool $isFirstMessage = false,
        public bool $isClearContext = false,
    ) {
    }
}
