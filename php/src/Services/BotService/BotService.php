<?php

declare(strict_types=1);

namespace App\Services\BotService;

use App\Services\BotService\Dto\RequestDto;
use App\Services\BotService\Dto\ResponseDto;
use App\Services\BotService\Dto\BotConfigDto;
use App\Services\BotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\BotService\Pipeline\HandlerPipeline;
use App\Services\BotService\Helpers\GptContextManager\GptContextManager;
use App\Services\BotService\Handlers\Enums\GptRolesEnum;
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
     * @var GptContextManager|null Менеджер контекста GPT.
     */
    private ?GptContextManager $contextManager;

    /**
     * @var HandlerPipeline|null Пайплайн обработчиков.
     */
    private ?HandlerPipeline $handlerPipeline;

    /**
     * @var BotConfigDto[] Кеш конфигов.
     */
    private static array $cachedConfigs = [];

    /**
     * Конструктор BotService.
     *
     * @param string $botId Идентификатор бота.
     * @param string $configId Идентификатор конфига.
     * @param GptContextManager|null $contextManager Менеджер контекста GPT.
     * @param HandlerPipeline|null $handlerPipeline Конвейер обработчиков.
     * @throws JsonException
     */
    public function __construct(
        private readonly string $botId,
        string $configId,
        ?GptContextManager $contextManager = null,
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
     * Возвращает список конфигов из папки Config, проиндексированный по BotConfigDto::id.
     *
     * @return BotConfigDto[] Список конфигов.
     */
    private static function getConfigs(): array
    {
        if (empty(self::$cachedConfigs)) {
            $configFiles = glob(__DIR__ . '/Config/*.php');
            $configs = [];

            foreach ($configFiles as $file) {
                /** @var BotConfigDto $config */
                $config = require $file;
                $configs[$config->id] = $config;
            }

            self::$cachedConfigs = $configs;
        }

        return self::$cachedConfigs;
    }

    /**
     * Возвращает массив, где ключ это BotConfigDto::id, а значение BotConfigDto::name.
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
     * @return GptContextManager Менеджер контекста GPT.
     * @throws JsonException
     */
    private function getContextManager(): GptContextManager
    {
        return $this->contextManager ??= new GptContextManager(contextId: $this->botId);
    }

    /**
     * Инициализирует внутреннее свойство $handlers из конфигов BotConfigDto::handlers.
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

        foreach ($config->handlers as $handlerConfig) {
            $handlerClass = $handlerConfig->class;
            $handlerInstance = new $handlerClass($handlerConfig->config);

            if (!$handlerInstance instanceof MessageHandlerInterface) {
                throw new InvalidArgumentException(
                    'Handler must implement ' . MessageHandlerInterface::class
                );
            }

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
