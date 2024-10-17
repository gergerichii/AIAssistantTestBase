<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Interfaces;

use App\Services\BotService\Request\Dto\HandlerRequestDto;
use App\Services\BotService\Request\Dto\HandlerResponseDto;
use App\Services\BotService\Request\Handlers\Enums\HandlerUsageEnum;

/**
 * Interface HandlerInterface
 * Определяет методы для обработки запросов и управления приоритетами обработчиков.
 */
interface HandlerInterface
{
    /**
     * Обрабатывает запрос и возвращает ответ.
     *
     * @param HandlerRequestDto $request Запрос для обработки.
     * @param HandlerRequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return HandlerResponseDto Ответ после обработки запроса.
     */
    public function handle(HandlerRequestDto $request, HandlerRequestDto $userRequest): HandlerResponseDto;

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
    public static function getHandlerUsage(): HandlerUsageEnum;

    /**
     * Устанавливает приоритет обработчика.
     *
     * @param int $priority Приоритет обработчика.
     */
    public function setPriority(int $priority): void;
}
