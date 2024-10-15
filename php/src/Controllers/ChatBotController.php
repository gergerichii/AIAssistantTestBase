<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dto\ChatConfigDto;
use App\Services\ContextManager\ContextManager;
use App\Services\IntelligentBotService\Dto\RequestDto;
use App\Services\IntelligentBotService\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\IntelligentBotService\Handlers\Dto\YandexGptConfigDto;
use App\Services\IntelligentBotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\IntelligentBotService\Handlers\OpenAIGptMessageHandler;
use App\Services\IntelligentBotService\Handlers\YandexGptMessageHandler;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\IntelligentBotService\BotService;
use App\Services\StoredConfig\ConfigManager;
use App\Services\StoredConfig\Storages\JsonConfigStorage;
use RuntimeException;

/**
 * Контроллер для обработки запросов к /chat_bot
 */
class ChatBotController
{
    private const string BOT_NAME = 'TestGptBot';
    private const array GPT_MODELS = [
        'YandexGpt' => [
            'name' => 'Yandex Gpt',
            'class' => YandexGptMessageHandler::class,
            'configClass' => YandexGptConfigDto::class,
            'configParams' => [
                'url' => 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion',
                'apiKey' => 'AQVNyRrW09cm9l1wGh4vECK5RlUGFpi3s4t9n-LS',
                'folderId' => 'b1go54pps8fofr8ebicn',
                'modelUri' => 'gpt://b1go54pps8fofr8ebicn/yandexgpt-lite/latest',
                'maxTokens' => 2000,
                'temperature' => '0.6',
                'stream' => false,
            ],
        ],
        'OpenAIGpt' => [
            'name' => 'OpenAI Gpt',
            'class' => OpenAIGptMessageHandler::class,
            'configClass' => OpenAIGptConfigDto::class,
            'configParams' => [
                'url' => '',
                'apiKey' => '',
                'modelName' => '',
            ],
        ],
    ];

    private ?ConfigManager $configManager = null;

    /**
     * Обрабатывает POST запросы к /chat_bot
     *
     * @param Request $request HTTP запрос
     * @param Response $response HTTP ответ
     * @return Response
     * @throws JsonException
     */
    public function handlePostMessage(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $userMessage = $body['message'] ?? '';
        $configManager = $this->getConfigManager();
        $contextManager = new ContextManager(self::BOT_NAME);

        if (isset($body['currentGptModel'])) {
            $storedConfigDto = new ChatConfigDto($body['currentGptModel']);
            $configManager->updateConfig(self::BOT_NAME, $storedConfigDto);
        } else {
            $storedConfigDto = new ChatConfigDto(key($this->getGptList()));
            $storedConfigDto = $configManager->registerConfig(self::BOT_NAME, $storedConfigDto);
        }

        if ($userMessage === '@clearContext') {
            $contextManager->deleteContext();
            $userMessage = '@handshake';
        }

        if ($userMessage === '@handshake') {
            $botResponse = 'Здравствуйте! Меня зовут Василий! Я ваш личный менеджер. Какие у вас есть вопросы?';
            $contextManager->updateContext([$botResponse]);
            sleep(4);
        } else {
            $botService = $this->getBotService($storedConfigDto->currentGptModel);

            $requestDto = new RequestDto(
                message: $userMessage,
                context: $contextManager->getContext(),
            );

            $botResponseResult = $botService->processRequest($requestDto);

            $contextManager->updateContext([...$botResponseResult->context, $botResponseResult->result]);

            $botResponse = $botResponseResult->result;
        }

        $response->getBody()->write(
            json_encode(
                [
                    'reply' => $botResponse,
                    'config' => [
                        'gptModelsList' => $this->getGptList(),
                        'currentGptModel' => $storedConfigDto->currentGptModel,
                    ]
                ],
                JSON_THROW_ON_ERROR
            )
        );

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getBotService(string $currentGptModel): BotService
    {
        $botHandlers = $this->getBotHandlers($currentGptModel);

        return new BotService($botHandlers);
    }

    /**
     * @param string $currentGptModel
     * @return MessageHandlerInterface[]
     */
    private function getBotHandlers(string $currentGptModel): array
    {
        $currentGptModelConfig = self::GPT_MODELS[$currentGptModel] ?? null;
        if ($currentGptModelConfig === null) {
            throw new RuntimeException('Unknown GPT model: ' . $currentGptModel);
        }

        /** @var MessageHandlerInterface $handlerClass */
        $handlerClass = $currentGptModelConfig['class'];
        /** @var ConfigDtoInterface $handlerConfigDtoClass */
        $handlerConfigDtoClass = $currentGptModelConfig['configClass'];
        $dtoParams = $currentGptModelConfig['configParams'];

        return [
            new $handlerClass(
                new $handlerConfigDtoClass(
                    ...$dtoParams,
                ),
            ),
        ];
    }

    private function getConfigManager(): ConfigManager
    {
        return $this->configManager ??= new ConfigManager(
            self::BOT_NAME,
            new JsonConfigStorage(),
        );
    }

    private function getGptList(): array
    {
        return array_combine(array_keys(self::GPT_MODELS), array_column(self::GPT_MODELS, 'name'));
    }
}
