<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler;

use App\Services\BotService\Core\SystemCommandHandler\Dto\RequestDto;
use App\Services\BotService\Core\SystemCommandHandler\Dto\ResponseDto;
use App\Services\BotService\Core\SystemCommandHandler\Interfaces\HandlerInterface;

/**
 * Class HandlerPipeline
 * Управляет последовательностью обработчиков для обработки системных команд.
 */
class HandlerPipeline
{
    /**
     * @var HandlerInterface[] $handlers Массив обработчиков.
     */
    private array $handlers;

    /**
     * Конструктор класса HandlerPipeline.
     *
     * @param HandlerInterface[] $handlers Список обработчиков, которые нужно
     * зарегистрировать в конвейере.
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Обрабатывает системную команду, проходя через конвейер обработчиков.
     *
     * @param RequestDto $request Запрос для обработки.
     * @return ResponseDto Ответ после обработки.
     */
    public function process(RequestDto $request): ResponseDto
    {
        // Логика обработки будет реализована позже
        return new ResponseDto();
    }

    /**
     * Добавляет новый обработчик в конвейер.
     *
     * @param HandlerInterface $handler Обработчик для добавления.
     */
    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }
}
