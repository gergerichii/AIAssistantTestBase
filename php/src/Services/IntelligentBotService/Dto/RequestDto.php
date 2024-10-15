<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Dto;

/**
 * Class RequestDto
 * DTO для представления запроса.
 */
readonly class RequestDto
{
    /**
     * @param string $message Сообщение запроса.
     * @param string[] $context Контекст запроса.
     */
    public function __construct(
        public string $message,
        public array $context = [],
    ) {
    }
}
