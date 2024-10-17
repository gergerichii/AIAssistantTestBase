<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Handlers\Dto;

use App\Services\BotService\Request\Enums\HandlerResponseStatusEnum;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

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
     * @param HandlerResponseStatusEnum $handlerResponseStatus Статус ответа обработчика.
     */
    public function __construct(
        public string $welcomeMessage,
        public HandlerResponseStatusEnum $handlerResponseStatus = HandlerResponseStatusEnum::FINAL,
    ) {}
}
