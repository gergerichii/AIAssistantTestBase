<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Handlers;

use App\Services\BotService\Request\Dto\HandlerRequestDto;
use App\Services\BotService\Request\Dto\HandlerResponseDto;
use App\Services\BotService\Request\Enums\HandlerResponseStatusEnum;
use App\Services\BotService\Request\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Request\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Request\Interfaces\HandlerInterface;

/**
 * Class OpenAIGptHandler
 * Реализация интерфейса HandlerInterface для обработки API ответов OpenAI GPT.
 */
class OpenAIGptHandler implements HandlerInterface
{
    /**
     * @var int Приоритет обработчика.
     */
    private int $priority = 0;

    /**
     * Конструктор класса OpenAIGptHandler.
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
     * @param HandlerRequestDto $request Запрос для обработки.
     * @param HandlerRequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return HandlerResponseDto Ответ после обработки запроса.
     */
    public function handle(HandlerRequestDto $request, HandlerRequestDto $userRequest): HandlerResponseDto
    {
        //TODO Реализовать обработку запроса
        return new HandlerResponseDto(
            result: 'Ответ от OpenAI GPT пока не реализован',
            addToContext: [],
            status: HandlerResponseStatusEnum::FINAL
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
