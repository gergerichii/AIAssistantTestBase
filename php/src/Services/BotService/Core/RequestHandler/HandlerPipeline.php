<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler;

use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\BotService\Core\RequestHandler\Dto\ResponseDto;
use App\Services\BotService\Core\RequestHandler\Enum\ResponseStatusEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Core\RequestHandler\Interfaces\HandlerInterface;
use Throwable;

/**
 * Class HandlerPipeline
 * Управляет последовательностью обработчиков для обработки запросов.
 */
class HandlerPipeline
{
    /**
     * @var HandlerInterface[] $handlers Массив обработчиков.
     */
    private array $handlers;

    /**
     * @var bool Флаг, указывающий, требуется ли сортировка.
     */
    private bool $needsSorting;

    /**
     * Конструктор класса HandlerPipeline.
     *
     * @param HandlerInterface[] $handlers Список обработчиков, которые нужно
     * зарегистрировать в конвейере.
     * @param ContextManager|null $contextManager Менеджер контекста GPT.
     */
    public function __construct(
        array $handlers = [],
        private readonly ?ContextManager $contextManager = null,
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

        $context = $this->contextManager?->getContext() ?? [];
        $addContext = $userRequest->context;
        $context = [...$context, ...$addContext];

        if (!empty($userRequest->message)) {
            $addContext = $this->addUserMessageToContext($addContext, $userRequest->message);
        }

        $request = new RequestDto(
            message: $userRequest->message,
            context: $context,
            isFirstMessage: $userRequest->isFirstMessage,
        );

        foreach ($this->handlers as $handler) {
            $response = $this->handleRequest($handler, $request, $userRequest, $context, $addContext);

            if ($this->isFinalResponse($response)) {
                $this->mergeToContextManager($addContext);

                return $response;
            }
        }

        return $this->createNoAnswerResponse();
    }

    /**
     * Добавляет новый обработчик в конвейер.
     *
     * @param HandlerInterface $handler Обработчик для добавления.
     */
    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
        $this->needsSorting = true;
    }

    /**
     * Удаляет обработчик из конвейера.
     *
     * @param HandlerInterface $handler Обработчик для удаления.
     */
    public function removeHandler(HandlerInterface $handler): void
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
     * Обрабатывает запрос с помощью обработчика.
     *
     * @param HandlerInterface $handler Обработчик.
     * @param RequestDto $request Запрос.
     * @param RequestDto $userRequest Исходный запрос пользователя.
     * @param array $context Общий Контекст запроса.
     * @param array $addContext Контекст который формируется во время обработки сообщения.
     * @return ResponseDto Ответ обработчика.
     */
    private function handleRequest(
        HandlerInterface $handler,
        RequestDto &$request,
        RequestDto $userRequest,
        array &$context,
        array &$addContext
    ): ResponseDto {
        do {
            try {
                $response = $handler->handle($request, $userRequest);
            } catch (Throwable $exception) {
                return $this->createErrorResponse($exception->getMessage());
            }

            $isSkipped = $response->status === ResponseStatusEnum::SKIPPED;
            $result = $isSkipped ? $request->message : $this->handleResponse($response->result);

            $isIntermediateResponse = $this->isIntermediateResponse($response);

            if (!empty($response->addToContext)) {
                $context = [...$context, ...$response->addToContext];
                $addContext = [...$addContext, ...$response->addToContext];
            }

            if (!$isSkipped) {
                $this->updateContext($context, $addContext, $isIntermediateResponse, $result, $response->data);
            }

            if ($isIntermediateResponse) {
                $request = $this->createIntermediateRequest(
                    result: $result,
                    isFirstMessage: $request->isFirstMessage,
                    context: $context
                );
            }
        } while ($response->status === ResponseStatusEnum::INTERMEDIATE_HANDLE_RESUME);

        return new ResponseDto(
            result: $result,
            addToContext: [],
            status: $response->status,
        );
    }

    /**
     * Проверяет, является ли ответ окончательным.
     *
     * @param ResponseDto $response Ответ для проверки.
     * @return bool True, если ответ окончательный.
     */
    private function isFinalResponse(ResponseDto $response): bool
    {
        return in_array($response->status, [
            ResponseStatusEnum::FINAL,
            ResponseStatusEnum::NO_ANSWER,
            ResponseStatusEnum::ERROR,
        ], true);
    }

    /**
     * Проверяет, является ли ответ промежуточным.
     *
     * @param ResponseDto $response Ответ для проверки.
     * @return bool True, если ответ промежуточный.
     */
    private function isIntermediateResponse(ResponseDto $response): bool
    {
        return in_array(
            $response->status,
            [
                ResponseStatusEnum::INTERMEDIATE,
                ResponseStatusEnum::INTERMEDIATE_HANDLE_RESUME,
                ResponseStatusEnum::SKIPPED,
            ],
            true
        );
    }

    /**
     * Сортирует обработчики по приоритету.
     */
    private function sortHandlers(): void
    {
        usort(
            $this->handlers,
            static function (HandlerInterface $a, HandlerInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            }
        );
        $this->needsSorting = false; // После сортировки сортировка не нужна
    }

    /**
     * Добавляет сообщение пользователя в контекст.
     *
     * @param array $context Контекст запроса.
     * @param string $message Сообщение пользователя.
     * @return array Обновленный контекст.
     */
    private function addUserMessageToContext(array $context, string $message): array
    {
        $contextItem = [
            'role' => GptRolesEnum::USER,
            'text' => $message,
        ];
        $context[] = $contextItem;

        return $context;
    }

    /**
     * Создает промежуточный запрос.
     *
     * @param string $result Результат предыдущего обработчика.
     * @param array $context Контекст обработки.
     * @return RequestDto Промежуточный запрос.
     */
    private function createIntermediateRequest(string $result, $isFirstMessage, array &$context): RequestDto
    {
        return new RequestDto(
            message: $result,
            context: $context,
            isFirstMessage: $isFirstMessage,
        );
    }

    /**
     * Создает ответ "Нет ответа".
     *
     * @return ResponseDto Ответ "Нет ответа".
     */
    private function createNoAnswerResponse(): ResponseDto
    {
        return new ResponseDto(
            result: 'Нет ответа. Повторите попытку позже',
            addToContext: [],
            status: ResponseStatusEnum::NO_ANSWER,
        );
    }

    /**
     * Создает ответ с ошибкой.
     *
     * @param string $result Результат обработки.
     * @return ResponseDto Ответ с ошибкой.
     */
    private function createErrorResponse(string $result): ResponseDto
    {
        return new ResponseDto(
            result: $result,
            addToContext: [],
            status: ResponseStatusEnum::ERROR,
        );
    }

    /**
     * Обновляет контекст с учетом нового ответа.
     *
     * @param array $context Контекст запроса.
     * @param bool $isIntermediateResponse Является ли ответ промежуточным.
     * @param string $result Результат обработки.
     * @param mixed $data Дополнительные данные.
     */
    private function updateContext(
        array &$context,
        array &$addContext,
        bool $isIntermediateResponse,
        string $result,
        mixed $data
    ): void {
        $addContext[] = $context[] = [
            'role' => $isIntermediateResponse ? GptRolesEnum::USER : GptRolesEnum::ASSISTANT,
            'text' => $result,
            'data' => $data,
        ];
    }

    /**
     * Добавляет элементы в сохраняемый контекст.
     *
     * @param array $addToContext Элементы для добавления в контекст.
     */
    private function mergeToContextManager(array $addToContext): void
    {
        if (!isset($this->contextManager)) {
            return;
        }

        foreach ($addToContext as $contextItem) {
            $role = $contextItem['role'] instanceof GptRolesEnum
                ? $contextItem['role']
                : GptRolesEnum::from($contextItem['role']);

            $this->contextManager?->addContextItem(
                role: $role,
                text: $contextItem['text'],
                data: $contextItem['data'] ?? null
            );
        }
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
}
