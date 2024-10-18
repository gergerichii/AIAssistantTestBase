<?php

declare(strict_types=1);

namespace App\Services\BotService;

use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\RequestHandler\Dto\RequestDto as HandlerRequestDto;
use App\Services\BotService\Core\RequestHandler\Enum\ResponseStatusEnum as HandlerResponseStatusEnum;
use App\Services\BotService\Core\RequestHandler\HandlerPipeline;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Core\RequestHandler\Interfaces\HandlerInterface;
use App\Services\BotService\Dto\ConfigDto as BotConfigDto;
use App\Services\BotService\Dto\RequestDto as BotRequestDto;
use App\Services\BotService\Dto\ResponseDto as BotResponseDto;
use App\Services\BotService\Enum\ResponseStatusEnum as BotResponseStatusEnum;
use InvalidArgumentException;
use JsonException;
use RuntimeException;

/**
 * Class BotService
 * Сервис для обработки запросов к боту.
 */
class BotService
{
    /**
     * @var ContextManager|null Менеджер контекста GPT.
     */
    private ?ContextManager $contextManager;

    /**
     * @var HandlerPipeline|null Пайплайн обработчиков.
     */
    private ?HandlerPipeline $handlerPipeline;

    /**
     * Конструктор BotService.
     *
     * @param string $botId Идентификатор бота.
     * @param BotConfigDto $config Конфигурация бота.
     * @param ContextManager|null $contextManager Менеджер контекста GPT.
     * @param HandlerPipeline|null $handlerPipeline Конвейер обработчиков.
     * @throws JsonException
     */
    public function __construct(
        private readonly string $botId,
        private readonly BotConfigDto $config,
        ?ContextManager $contextManager = null,
        ?HandlerPipeline $handlerPipeline = null,
    ) {
        $this->handlerPipeline = $handlerPipeline;
        $this->contextManager = $contextManager;
        $this->initializeHandlers();
    }

    /**
     * Обрабатывает запрос к боту.
     *
     * @param BotRequestDto $request Запрос к боту.
     * @return BotResponseDto Ответ от бота.
     */
    public function processRequest(BotRequestDto $request): BotResponseDto
    {
        if ($request->isClearContext) {
            $this->contextManager->deleteContext();
        }

        $handlerRequest = new HandlerRequestDto(
            message: $request->message,
            isFirstMessage: $request->isFirstMessage,
        );

        $handlerResponse = $this->handlerPipeline->process(userRequest: $handlerRequest);

        return new BotResponseDto(
            result: $handlerResponse->result,
            status: match ($handlerResponse->status) {
                HandlerResponseStatusEnum::FINAL => BotResponseStatusEnum::OK,
                default => BotResponseStatusEnum::ERROR,
            },
        );
    }

    /**
     * Возвращает текущий контекст.
     *
     * @return array<array{role: GptRolesEnum, text: string, data: ?array<string>}> Массив контекста.
     * @throws JsonException
     */
    public function getContext(): array
    {
        return $this->getContextManager()->getContext();
    }

    /**
     * Возвращает менеджер контекста GPT.
     *
     * @return ContextManager Менеджер контекста GPT.
     * @throws JsonException
     */
    private function getContextManager(): ContextManager
    {
        return $this->contextManager ??= new ContextManager(contextId: $this->botId);
    }

    /**
     * Инициализирует внутреннее свойство $handlers из конфигов BotConfigDto::handlers.
     *
     * @throws RuntimeException Если конфиг с указанным идентификатором не найден.
     * @throws JsonException
     */
    private function initializeHandlers(): void
    {
        $handlerNumber = 0;

        foreach ($this->config->requestHandlers as $handlerConfig) {
            $handlerClass = $handlerConfig->class;
            $handlerInstance = new $handlerClass($handlerConfig->config);

            if (!$handlerInstance instanceof HandlerInterface) {
                throw new InvalidArgumentException(
                    'Handler must implement ' . HandlerInterface::class
                );
            }

            $handlerInstance->setPriority(priority: $handlerNumber++);

            $this->getHandlerPipeline()->addHandler(handler: $handlerInstance);
        }
    }

    /**
     * @throws JsonException
     */
    private function getHandlerPipeline(): HandlerPipeline
    {
        return $this->handlerPipeline ??= new HandlerPipeline(contextManager: $this->getContextManager());
    }
}
