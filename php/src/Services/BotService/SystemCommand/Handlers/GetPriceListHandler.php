<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Handlers;

use App\Services\BotService\SystemCommand\Dto\SystemCommandRequestDto;
use App\Services\BotService\SystemCommand\Dto\SystemCommandResponseDto;
use App\Services\BotService\SystemCommand\Interfaces\HandlerInterface;

/**
 * Class GetPriceListHandler
 * Обработчик для команды получения прайс-листа.
 */
class GetPriceListHandler implements HandlerInterface
{
    /**
     * Обрабатывает системную команду и возвращает ответ.
     *
     * @param SystemCommandRequestDto $request Запрос для обработки.
     * @return SystemCommandResponseDto Ответ после обработки.
     */
    public function handle(SystemCommandRequestDto $request): SystemCommandResponseDto
    {
        // Логика обработки будет реализована позже
        return new SystemCommandResponseDto();
    }
}
