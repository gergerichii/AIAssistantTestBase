<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Handlers;

use App\Services\BotService\Helpers\GptContextManager\GptContextManager;
use App\Services\BotService\Request\Dto\HandlerRequestDto;
use App\Services\BotService\Request\Dto\HandlerResponseDto;
use App\Services\BotService\Request\Enums\HandlerResponseStatusEnum;
use App\Services\BotService\Request\Handlers\Dto\FirstMessageConfigDto;
use App\Services\BotService\Request\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Request\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Request\Interfaces\HandlerInterface;

/**
 * Class HandshakeHandler
 * Реализация интерфейса HandlerInterface для обработки первого сообщения.
 */
class HandshakeHandler implements HandlerInterface
{
    /**
     * @var int Приоритет обработчика.
     */
    private int $priority = 0;

    /**
     * Конструктор класса HandshakeHandler.
     *
     * @param FirstMessageConfigDto $config Настройки по умолчанию.
     */
    public function __construct(
        private FirstMessageConfigDto $config,
    ) {
    }

    /**
     * Обрабатывает запрос и возвращает ответ.
     *
     * @param HandlerRequestDto $request Запрос для обработки.
     * @param HandlerRequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return HandlerResponseDto Ответ после обработки запроса.
     */
    public function handle(HandlerRequestDto $request, HandlerRequestDto $userRequest): HandlerResponseDto
    {
        return new HandlerResponseDto(
            result: $request->isFirstMessage ? $this->config->welcomeMessage : '',
            addToContext: $request->isFirstMessage ?[GptContextManager::createContextItem(
                role: GptRolesEnum::USER,
                text: 'bot: Поздоровайся с клиентом первый!'
            )] : [],
            status: $request->isFirstMessage ? $this->config->handlerResponseStatus : HandlerResponseStatusEnum::SKIPPED,
        );
    }

    /**
     * Устанавливает приоритет обработчика.
     *
     * @param int $priority Приоритет обработчика.
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Возвращает приоритет обработчика.
     *
     * @return int Приоритет обработчика.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Получает признак использования обработчика.
     *
     * @return HandlerUsageEnum Признак использования обработчика.
     */
    public static function getHandlerUsage(): HandlerUsageEnum
    {
        return HandlerUsageEnum::STATIC_HANDLER;
    }
}
