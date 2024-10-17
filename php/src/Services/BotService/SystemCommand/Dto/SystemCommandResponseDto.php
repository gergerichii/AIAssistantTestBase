<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Dto;

/**
 * Class SystemCommandResponseDto
 * DTO для представления ответа на системную команду.
 */
readonly class SystemCommandResponseDto
{
    /**
     * @param string $result Результат обработки команды.
     */
    public function __construct(
        public string $result = '',
    ) {}
}
