<?php

declare(strict_types=1);

namespace App\Services\BotService\Handlers;

use App\Services\BotService\Dto\RequestDto;
use App\Services\BotService\Dto\ResponseDto;
use App\Services\BotService\Enums\ResponseStatusEnum;
use App\Services\BotService\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;

/**
 * Class OpenAIGptMessageHandler
 * Реализация интерфейса HandlerInterface для обработки API ответов OpenAI GPT.
 */
class OpenAIGptMessageHandler implements MessageHandlerInterface
{
    /**
     * @var int Приоритет обработчика.
     */
    private int $priority = 0;

    /**
     * Конструктор класса OpenAIGptMessageHandler.
     *
     * @param OpenAIGptConfigDto $config Настройки по умолчанию.
     */
    public function __construct(
        private OpenAIGptConfigDto $config,
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
        //TODO Реализовать обработку запроса
        return new ResponseDto(
            result: 'Ответ от OpenAI GPT пока не реализован',
            addToContext: [],
            status: ResponseStatusEnum::FINAL
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
        return HandlerUsageEnum::PAID_MODEL_GPT;
    }
}
