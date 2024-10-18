<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler\Dto;

/**
 * Class ResponseDto
 * DTO для представления ответа на системную команду.
 */
readonly class ResponseDto
{
    /**
     * @param string $result Результат обработки команды.
     */
    public function __construct(
        public string $result = '',
    ) {}
}
