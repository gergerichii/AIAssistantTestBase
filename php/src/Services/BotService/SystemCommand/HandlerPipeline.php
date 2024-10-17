<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand;

use App\Services\BotService\SystemCommand\Interfaces\HandlerInterface;
use App\Services\BotService\SystemCommand\Dto\SystemCommandRequestDto;
use App\Services\BotService\SystemCommand\Dto\SystemCommandResponseDto;

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
     * @param SystemCommandRequestDto $request Запрос для обработки.
     * @return SystemCommandResponseDto Ответ после обработки.
     */
    public function process(SystemCommandRequestDto $request): SystemCommandResponseDto
    {
        // Логика обработки будет реализована позже
        return new SystemCommandResponseDto();
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
