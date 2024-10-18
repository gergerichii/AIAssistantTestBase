<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Handlers;

use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\BotService\Core\RequestHandler\Dto\ResponseDto;
use App\Services\BotService\Core\RequestHandler\Enums\HandlerResponseStatusEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Core\RequestHandler\Interfaces\HandlerInterface;

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
