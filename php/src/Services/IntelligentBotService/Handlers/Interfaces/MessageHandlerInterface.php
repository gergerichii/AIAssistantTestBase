<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Handlers\Interfaces;

use App\Services\IntelligentBotService\Dto\RequestDto;
use App\Services\IntelligentBotService\Dto\ResponseDto;
use App\Services\IntelligentBotService\Handlers\Enums\HandlerUsageEnum;

/**
 * Interface HandlerInterface
 * Определяет методы для обработки запросов и управления приоритетами обработчиков.
 */
interface MessageHandlerInterface
{
    /**
     * Обрабатывает запрос и возвращает ответ.
     *
     * @param RequestDto $request Запрос для обработки.
     * @return ResponseDto Ответ после обработки запроса.
     */
    public function handle(RequestDto $request): ResponseDto;

    /**
     * Возвращает приоритет обработчика.
     *
     * @return int Приоритет обработчика.
     */
    public function getPriority(): int;

    /**
     * Получает признак использования обработчика.
     *
     * @return HandlerUsageEnum Признак использования обработчика.
     */
    public function getHandlerUsage(): HandlerUsageEnum;
}
