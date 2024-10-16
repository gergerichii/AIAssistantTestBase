<?php

declare(strict_types=1);

namespace App\Services\BotService\Pipeline;

use App\Services\BotService\Dto\RequestDto;
use App\Services\BotService\Dto\ResponseDto;
use App\Services\BotService\Enums\ResponseStatusEnum;
use App\Services\BotService\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\BotService\Helpers\GptContextManager\GptContextManager;

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
     * @param GptContextManager|null $contextManager Менеджер контекста GPT.
     */
    public function __construct(
        array $handlers = [],
        private readonly ?GptContextManager $contextManager = null,
    ) {
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

        $context = $userRequest->context;
        if ($this->contextManager) {
            $context = array_merge($context, $this->contextManager->getContext());
        }

        $contextItem = [
            'role' => GptRolesEnum::USER,
            'text' => $userRequest->message,
        ];

        $context[] = $contextItem;

        if ($this->contextManager) {
            $this->contextManager->addContextItem(
                role: GptRolesEnum::USER,
                text: $userRequest->message
            );
        }

        $request = new RequestDto(
            message: $userRequest->message,
            context: $context,
        );

        foreach ($this->handlers as $handler) {
            do {
                $response = $handler->handle($request);

                if ($response->status === ResponseStatusEnum::FINAL) {
                    return $this->createFinalResponse($response->result, $context);
                }

                if ($response->status === ResponseStatusEnum::INTERMEDIATE) {
                    $request = $this->createIntermediateRequest($userRequest, $response->result, $context, GptRolesEnum::SYSTEM);
                }

                if ($response->status === ResponseStatusEnum::NO_ANSWER) {
                    return $this->createNoAnswerResponse($context);
                }
            } while ($response->status === ResponseStatusEnum::INTERMEDIATE_HANDLE_RESUME);
        }

        return $this->createNoAnswerResponse($context);
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

    /**
     * Обрабатывает ответ, возвращая результат в виде строки.
     *
     * @param string $response Ответ для обработки.
     * @return string Обработанный результат.
     */
    private function handleResponse(string $response): string
    {
        // Заглушка, возвращающая исходную строку
        return $response;
    }

    /**
     * Создает окончательный ответ.
     *
     * @param string $result Результат обработки.
     * @param array $context Контекст обработки.
     * @return ResponseDto Окончательный ответ.
     */
    private function createFinalResponse(string $result, array $context): ResponseDto
    {
        $finalResult = $this->handleResponse($result);
        $contextItem = [
            'role' => GptRolesEnum::ASSISTANT,
            'text' => $finalResult,
        ];
        $context[] = $contextItem;

        if ($this->contextManager) {
            $this->contextManager->addContextItem(
                role: GptRolesEnum::ASSISTANT,
                text: $finalResult
            );
        }

        return new ResponseDto(
            result: $finalResult,
            context: $context,
            status: ResponseStatusEnum::FINAL,
        );
    }

    /**
     * Создает промежуточный запрос.
     *
     * @param RequestDto $userRequest Исходный запрос пользователя.
     * @param string $result Результат обработки.
     * @param array $context Контекст обработки.
     * @param GptRolesEnum $role Роль в контексте.
     * @return RequestDto Промежуточный запрос.
     */
    private function createIntermediateRequest(RequestDto $userRequest, string $result, array &$context, GptRolesEnum $role): RequestDto
    {
        $intermediateResult = $this->handleResponse($result);
        $contextItem = [
            'role' => $role,
            'text' => $intermediateResult,
        ];
        $context[] = $contextItem;

        if ($this->contextManager) {
            $this->contextManager->addContextItem(
                role: $role,
                text: $intermediateResult
            );
        }

        return new RequestDto(
            message: $userRequest->message,
            context: $context,
        );
    }

    /**
     * Создает ответ "Нет ответа".
     *
     * @param array $context Контекст обработки.
     * @return ResponseDto Ответ "Нет ответа".
     */
    private function createNoAnswerResponse(array $context): ResponseDto
    {
        return new ResponseDto(
            result: 'Нет ответа. Повторите попытку позже',
            context: $context,
            status: ResponseStatusEnum::NO_ANSWER,
        );
    }
}
