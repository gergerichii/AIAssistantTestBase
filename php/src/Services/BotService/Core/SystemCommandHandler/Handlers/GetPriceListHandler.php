<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler\Handlers;

use App\Services\BotService\Core\SystemCommandHandler\Dto\RequestDto;
use App\Services\BotService\Core\SystemCommandHandler\Dto\ResponseDto;
use App\Services\BotService\Core\SystemCommandHandler\Interfaces\HandlerInterface;

/**
 * Class GetPriceListHandler
 * Обработчик для команды получения прайс-листа.
 */
class GetPriceListHandler implements HandlerInterface
{
    /**
     * Обрабатывает системную команду и возвращает ответ.
     *
     * @param RequestDto $request Запрос для обработки.
     * @return ResponseDto Ответ после обработки.
     */
    public function handle(RequestDto $request): ResponseDto
    {
        // Логика обработки будет реализована позже
        return new ResponseDto();
    }
}
