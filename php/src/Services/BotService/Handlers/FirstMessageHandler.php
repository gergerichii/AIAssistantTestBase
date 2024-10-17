<?php

declare(strict_types=1);

namespace App\Services\BotService\Handlers;

use App\Services\BotService\Dto\RequestDto;
use App\Services\BotService\Dto\ResponseDto;
use App\Services\BotService\Enums\ResponseStatusEnum;
use App\Services\BotService\Handlers\Dto\FirstMessageConfigDto;
use App\Services\BotService\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Helpers\GptContextManager\GptContextManager;

/**
 * Class FirstMessageHandler
 * Реализация интерфейса MessageHandlerInterface для обработки первого сообщения.
 */
class FirstMessageHandler implements MessageHandlerInterface
{
    /**
     * @var int Приоритет обработчика.
     */
    private int $priority = 0;

    /**
     * Конструктор класса FirstMessageHandler.
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
            addToContext: $request->isFirstMessage ?[GptContextManager::createContextItem(
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
