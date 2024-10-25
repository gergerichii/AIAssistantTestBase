<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\AppConstants;
use App\Services\BotService\BotService;
use App\Services\BotService\Dto\RequestDto as BotRequestDto;
use App\Services\BotService\Helpers\BotConfigManager;
use App\Services\BotService\Helpers\ConfigManager\Drivers\PhpFileDriver;
use Aura\Session\Session;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Контроллер для обработки запросов к /chat_bot
 */
class ChatBotController
{
    private const string BOT_NAME = 'TestGptBot';
    private const string CONFIG_ID_SESSION_KEY = 'currentBotConfigId';
    private ?BotConfigManager $configManager = null;
    public function __construct(
        private readonly Session $session,
    ){
    }

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
        $session = $this->session->getSegment(self::class);
        $body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $userMessage = $body['message'] ?? '';
        $isClearContext = $userMessage === '@clearContext';
        $isFirstMessage = $userMessage === '@handshake' || $isClearContext;

        $configManager = $this->getConfigManager();
        $configList = $configManager->getConfigsNames();
        $botConfigId = $body['currentBotConfig']
            ?? $session->get(self::CONFIG_ID_SESSION_KEY, key($configList));

        $botConfig = $configManager->getConfigById($botConfigId);
        $session->set(self::CONFIG_ID_SESSION_KEY, $botConfigId);

        $requestDto = new BotRequestDto(
            message: $isFirstMessage ? '' : $userMessage,
            isFirstMessage: $isFirstMessage,
            isClearContext: $isClearContext,
        );

        $botService = new BotService(botId: self::BOT_NAME, config: $botConfig);
        $botResponseResult = $botService->processRequest($requestDto);

        $botResponse = $botResponseResult->result;

        $response->getBody()->write(
            json_encode(
                [
                    'reply' => $botResponse,
                    'config' => [
                        'gptModelsList' => $configList,
                        'currentBotConfig' => $botConfigId,
                    ]
                ],
                JSON_THROW_ON_ERROR
            )
        );

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getConfigManager(): BotConfigManager
    {
        return $this->configManager ??= new BotConfigManager(
            new PhpFileDriver(AppConstants::APP_CONFIG_DIR . 'BotService' . DIRECTORY_SEPARATOR),
        );
    }
}
