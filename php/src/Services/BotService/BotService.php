<?php

declare(strict_types=1);

namespace App\Services\BotService;

use App\Services\BotService\Dto\ConfigDto;
use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\BotService\Core\RequestHandler\Dto\ResponseDto;
use App\Services\BotService\Core\RequestHandler\HandlerPipeline;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Core\RequestHandler\Interfaces\HandlerInterface;
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
     * @var ConfigDto[] Кеш конфигов.
     */
    private static array $cachedConfigs = [];

    /**
     * Конструктор BotService.
     *
     * @param string $botId Идентификатор бота.
     * @param string $configId Идентификатор конфига.
     * @param ContextManager|null $contextManager Менеджер контекста GPT.
     * @param HandlerPipeline|null $handlerPipeline Конвейер обработчиков.
     * @throws JsonException
     */
    public function __construct(
        private readonly string $botId,
        string $configId,
        ?ContextManager $contextManager = null,
        ?HandlerPipeline $handlerPipeline = null,
    ) {
        $this->handlerPipeline = $handlerPipeline;
        $this->contextManager = $contextManager;
        $this->initializeHandlers(configId: $configId);
    }

    /**
     * Обрабатывает запрос к боту.
     *
     * @param RequestDto $request Запрос к боту.
     * @return ResponseDto Ответ от бота.
     */
    public function processRequest(RequestDto $request): ResponseDto
    {
        return $this->handlerPipeline->process(userRequest: $request);
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
     * Очищает текущий контекст.
     * @throws JsonException
     */
    public function newContext(): void
    {
        $this->getContextManager()->deleteContext();
    }

    /**
     * Возвращает список конфигов из папки Config, проиндексированный по ConfigDto::id.
     *
     * @return ConfigDto[] Список конфигов.
     */
    private static function getConfigs(): array
    {
        if (empty(self::$cachedConfigs)) {
            $configFiles = glob(__DIR__ . '/Config/*.php');
            $configs = [];

            foreach ($configFiles as $file) {
                /** @var ConfigDto $config */
                $config = require $file;
                $configs[$config->id] = $config;
            }

            self::$cachedConfigs = $configs;
        }

        return self::$cachedConfigs;
    }

    /**
     * Возвращает массив, где ключ это ConfigDto::id, а значение ConfigDto::name.
     *
     * @return array<string, string> Массив идентификаторов и имен конфигов.
     */
    public static function getConfigNames(): array
    {
        $configs = self::getConfigs();
        $configNames = [];

        foreach ($configs as $config) {
            $configNames[$config->id] = $config->name;
        }

        return $configNames;
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
     * Инициализирует внутреннее свойство $handlers из конфигов ConfigDto::handlers.
     *
     * @param string $configId Идентификатор конфига.
     * @throws RuntimeException Если конфиг с указанным идентификатором не найден.
     * @throws JsonException
     */
    private function initializeHandlers(string $configId): void
    {
        $configs = self::getConfigs();

        if (!isset($configs[$configId])) {
            throw new RuntimeException("Config with ID {$configId} not found.");
        }

        $config = $configs[$configId];

        $handlerNumber = 0;

        foreach ($config->requestHandlers as $handlerConfig) {
            $handlerClass = $handlerConfig->class;
            $handlerInstance = new $handlerClass($handlerConfig->config);

            if (!$handlerInstance instanceof HandlerInterface) {
                throw new InvalidArgumentException(
                    'Handler must implement ' . HandlerInterface::class
                );
            }

            $handlerInstance->setPriority($handlerNumber++);

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
