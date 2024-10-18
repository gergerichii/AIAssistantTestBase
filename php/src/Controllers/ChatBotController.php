<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dto\ChatConfigDto;
use App\Services\BotService\BotService;
use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\StoredConfigService\ConfigManager;
use App\Services\StoredConfigService\Storages\JsonConfigStorage;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Контроллер для обработки запросов к /chat_bot
 */
class ChatBotController
{
    private const string BOT_NAME = 'TestGptBot';
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
        $currentConfig = $body['currentBotConfig'] ?? null;
        $configList = BotService::getConfigNames();
        $contextManager = new ContextManager(self::BOT_NAME);

        if ($currentConfig !== null) {
            $storedConfigDto = new ChatConfigDto($currentConfig);
            $configManager->updateConfig(self::BOT_NAME, $storedConfigDto);
        } else {
            $storedConfigDto = new ChatConfigDto(key($configList));
            $storedConfigDto = $configManager->registerConfig(self::BOT_NAME, $storedConfigDto);
        }

        if ($userMessage === '@clearContext') {
            $contextManager->deleteContext();
            $userMessage = '@handshake';
        }

        $botService = new BotService(self::BOT_NAME, $storedConfigDto->currentBotConfig, $contextManager);

        $isFirstMessage = $userMessage === '@handshake';

        $requestDto = new RequestDto(
            message: $isFirstMessage ? '' : $userMessage,
            context: [],
            isFirstMessage: $isFirstMessage,
        );

        $botResponseResult = $botService->processRequest($requestDto);

        $botResponse = $botResponseResult->result;

        $response->getBody()->write(
            json_encode(
                [
                    'reply' => $botResponse,
                    'config' => [
                        'gptModelsList' => $configList,
                        'currentBotConfig' => $storedConfigDto->currentBotConfig,
                    ]
                ],
                JSON_THROW_ON_ERROR
            )
        );

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getConfigManager(): ConfigManager
    {
        return $this->configManager ??= new ConfigManager(
            self::BOT_NAME,
            new JsonConfigStorage(),
        );
    }
}
