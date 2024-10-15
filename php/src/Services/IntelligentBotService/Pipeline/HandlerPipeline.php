<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Pipeline;

use App\Services\IntelligentBotService\Dto\RequestDto;
use App\Services\IntelligentBotService\Dto\ResponseDto;
use App\Services\IntelligentBotService\Enums\ResponseStatusEnum;
use App\Services\IntelligentBotService\Handlers\Interfaces\MessageHandlerInterface;
use LogicException;

//TODO доделать цикл обработки так, чтобы после каждого обработчика парсить ответ на специальные теги, которые можно
// преобразовывать в дополнительные команды обработки (на пример калькулятор или запрос к БД и т.д.)
/**
 * Class HandlerPipeline
 * Управляет последовательностью обработчиков для обработки запросов.
 */
class HandlerPipeline
{
    /**
     * @var MessageHandlerInterface[] $handlers Массив обработчиков.
     */
    private array $handlers;

    /**
     * @var bool Флаг, указывающий, требуется ли сортировка.
     */
    private bool $needsSorting;

    /**
     * Конструктор класса HandlerPipeline.
     *
     * @param MessageHandlerInterface[] $handlers Список обработчиков, которые нужно
     * зарегистрировать в конвейере.
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
        $this->needsSorting = true; // Флаг, чтобы сортировать при первой необходимости
    }

    /**
     * Обрабатывает запрос, проходя через конвейер обработчиков.
     *
     * @param RequestDto $userRequest Запрос для обработки.
     * @return ResponseDto Окончательный или промежуточный ответ, если
     * окончательного нет.
     */
    public function process(RequestDto $userRequest): ResponseDto
    {
        // Проверяем, нужно ли сортировать обработчики
        if ($this->needsSorting) {
            $this->sortHandlers();
        }

        $request = $userRequest;
        $response = new ResponseDto(
            result:'Нет ответа. Повторите попытку позже',
            context: [...$userRequest->context, $userRequest->message],
            status: ResponseStatusEnum::NO_ANSWER
        );

        // Перебираем обработчики по приоритету
        foreach ($this->handlers as $handler) {
            $response = $handler->handle($request);

            if ($response->status === ResponseStatusEnum::FINAL) {
                return $response; // Окончательный ответ
            }

            $request = new RequestDto(
                message: $response->result,
                context: $response->context,
            );
        }

        return $response;
    }

    /**
     * Добавляет новый обработчик в конвейер.
     *
     * @param MessageHandlerInterface $handler Обработчик для добавления.
     */
    public function addHandler(MessageHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
        $this->needsSorting = true;
    }

    /**
     * Удаляет обработчик из конвейера.
     *
     * @param MessageHandlerInterface $handler Обработчик для удаления.
     */
    public function removeHandler(MessageHandlerInterface $handler): void
    {
        $this->handlers = array_filter(
            $this->handlers,
            static function ($existingHandler) use ($handler) {
                return $existingHandler !== $handler;
            }
        );
        $this->needsSorting = true; // Флаг для повторной сортировки после удаления
    }

    /**
     * Сортирует обработчики по приоритету.
     */
    private function sortHandlers(): void
    {
        usort(
            $this->handlers,
            static function (MessageHandlerInterface $a, MessageHandlerInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            }
        );
        $this->needsSorting = false; // После сортировки сортировка не нужна
    }
}
