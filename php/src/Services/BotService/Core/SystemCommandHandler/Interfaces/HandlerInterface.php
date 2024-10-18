<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler\Interfaces;

use App\Services\BotService\Core\SystemCommandHandler\Dto\RequestDto;
use App\Services\BotService\Core\SystemCommandHandler\Dto\ResponseDto;

/**
 * Interface HandlerInterface
 * Определяет методы для обработки системных команд.
 */
interface HandlerInterface
{
    /**
     * Обрабатывает системную команду и возвращает ответ.
     *
     * @param RequestDto $request Запрос для обработки.
     * @return ResponseDto Ответ после обработки.
     */
    public function handle(RequestDto $request): ResponseDto;
}
