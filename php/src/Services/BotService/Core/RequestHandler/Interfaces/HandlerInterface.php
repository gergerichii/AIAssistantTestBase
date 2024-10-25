<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Interfaces;

use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\BotService\Core\RequestHandler\Dto\ResponseDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;

/**
 * Interface HandlerInterface
 * Определяет методы для обработки запросов и управления приоритетами обработчиков.
 */
interface HandlerInterface
{
    /**
     * Обрабатывает запрос и возвращает ответ.
     *
     * @param RequestDto $request Запрос для обработки.
     * @param RequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return ResponseDto Ответ после обработки запроса.
     */
    public function handle(RequestDto $request, RequestDto $userRequest): ResponseDto;

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
