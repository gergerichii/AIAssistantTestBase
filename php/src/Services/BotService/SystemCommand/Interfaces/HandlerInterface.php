<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Interfaces;

use App\Services\BotService\SystemCommand\Dto\SystemCommandRequestDto;
use App\Services\BotService\SystemCommand\Dto\SystemCommandResponseDto;

/**
 * Interface HandlerInterface
 * Определяет методы для обработки системных команд.
 */
interface HandlerInterface
{
    /**
     * Обрабатывает системную команду и возвращает ответ.
     *
     * @param SystemCommandRequestDto $request Запрос для обработки.
     * @return SystemCommandResponseDto Ответ после обработки.
     */
    public function handle(SystemCommandRequestDto $request): SystemCommandResponseDto;
}
