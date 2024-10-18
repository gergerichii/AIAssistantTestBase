<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Handlers\Dto;

use App\Services\BotService\Core\RequestHandler\Enum\ResponseStatusEnum;
use App\Services\BotService\Interfaces\ConfigDtoInterface;

/**
 * Class FirstMessageConfigDto
 * 
 * DTO для хранения настроек обработчика первого сообщения.
 */
readonly class FirstMessageConfigDto implements ConfigDtoInterface
{
    /**
     * DTO для хранения настроек обработчика первого сообщения.
     *
     * @param string $welcomeMessage Приветственное сообщение.
     * @param ResponseStatusEnum $handlerResponseStatus Статус ответа обработчика.
     */
    public function __construct(
        public string $welcomeMessage,
        public ResponseStatusEnum $handlerResponseStatus = ResponseStatusEnum::FINAL,
    ) {}
}
