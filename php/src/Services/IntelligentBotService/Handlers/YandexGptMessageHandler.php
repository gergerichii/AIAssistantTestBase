<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Handlers;

use App\Services\IntelligentBotService\Dto\RequestDto;
use App\Services\IntelligentBotService\Dto\ResponseDto;
use App\Services\IntelligentBotService\Enums\ResponseStatusEnum;
use App\Services\IntelligentBotService\Handlers\Dto\YandexGptConfigDto;
use App\Services\IntelligentBotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\IntelligentBotService\Handlers\Enums\HandlerUsageEnum;

/**
 * Class YandexGptMessageHandler
 * Реализация интерфейса MessageHandlerInterface для обработки API ответов Yandex GPT.
 */
readonly class YandexGptMessageHandler implements MessageHandlerInterface
{
    /**
     * Конструктор класса YandexGptMessageHandler.
     *
     * @param YandexGptConfigDto $config Настройки по умолчанию.
     */
    public function __construct(
        private YandexGptConfigDto $config,
    ) {
    }

    /**
     * Обрабатывает запрос и возвращает ответ.
     *
     * @param RequestDto $request Запрос для обработки.
     * @return ResponseDto Ответ после обработки запроса.
     */
    public function handle(RequestDto $request): ResponseDto
    {
        //TODO Реализовать обработку запроса
        return new ResponseDto(
            result: 'Ответ от Yandex GPT',
            context: [...$request->context, $request->message],
            status: ResponseStatusEnum::FINAL
        );
    }

    /**
     * Возвращает приоритет обработчика.
     *
     * @return int Приоритет обработчика.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * Получает признак использования обработчика.
     *
     * @return HandlerUsageEnum Признак использования обработчика.
     */
    public function getHandlerUsage(): HandlerUsageEnum
    {
        return HandlerUsageEnum::PAID_MODEL_GPT;
    }
}
