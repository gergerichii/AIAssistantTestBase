<?php

declare(strict_types=1);

namespace App\Services\BotService\Handlers;

use App\Services\BotService\Dto\RequestDto;
use App\Services\BotService\Dto\ResponseDto;
use App\Services\BotService\Enums\ResponseStatusEnum;
use App\Services\BotService\Handlers\Dto\YandexGptConfigDto;
use App\Services\BotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;

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
            context: $request->context,
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
    public static function getHandlerUsage(): HandlerUsageEnum
    {
        return HandlerUsageEnum::PAID_MODEL_GPT;
    }
}
