<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Handlers;

use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\BotService\Core\RequestHandler\Dto\ResponseDto;
use App\Services\BotService\Core\RequestHandler\Enum\ResponseStatusEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\FirstMessageConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Core\RequestHandler\Interfaces\HandlerInterface;

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
     * @param RequestDto $request Запрос для обработки.
     * @param RequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return ResponseDto Ответ после обработки запроса.
     */
    public function handle(RequestDto $request, RequestDto $userRequest): ResponseDto
    {
        return new ResponseDto(
            result: $request->isFirstMessage ? $this->config->welcomeMessage : '',
            addToContext: $request->isFirstMessage ?[ContextManager::createContextItem(
                role: GptRolesEnum::USER,
                text: 'bot: Поздоровайся с клиентом первый!'
            )] : [],
            status: $request->isFirstMessage ? $this->config->handlerResponseStatus : ResponseStatusEnum::SKIPPED,
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
