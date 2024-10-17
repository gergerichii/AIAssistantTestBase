<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Handlers;

use App\Services\BotService\Helpers\GptContextManager\GptContextManager;
use App\Services\BotService\Request\Dto\HandlerRequestDto;
use App\Services\BotService\Request\Dto\HandlerResponseDto;
use App\Services\BotService\Request\Enums\HandlerResponseStatusEnum;
use App\Services\BotService\Request\Handlers\Dto\YandexGptConfigDto;
use App\Services\BotService\Request\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Request\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Request\Interfaces\HandlerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

/**
 * Class YandexGptHandler
 * Реализация интерфейса HandlerInterface для обработки API ответов Yandex GPT.
 */
class YandexGptHandler implements HandlerInterface
{
    /**
     * @var int Приоритет обработчика.
     */
    private int $priority = 0;

    /**
     * @var Client|null HTTP клиент для выполнения запросов.
     */
    private ?Client $httpClient;

    /**
     * Конструктор класса YandexGptHandler.
     *
     * @param YandexGptConfigDto $config Настройки по умолчанию.
     * @param Client|null $httpClient HTTP клиент для выполнения запросов.
     */
    public function __construct(
        private readonly YandexGptConfigDto $config,
        ?Client $httpClient = null,
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Обрабатывает запрос и возвращает ответ.
     *
     * @param HandlerRequestDto $request Запрос для обработки.
     * @param HandlerRequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return HandlerResponseDto Ответ после обработки запроса.
     * @throws JsonException
     */
    public function handle(HandlerRequestDto $request, HandlerRequestDto $userRequest): HandlerResponseDto
    {
        $httpClient = $this->getHttpClient();

        $messages = array_map(
            static fn($contextItem) => [
                'role' => $contextItem['role']->value,
                'text' => $contextItem['text'],
            ],
            [
                GptContextManager::createContextItem(
                    GptRolesEnum::SYSTEM, $this->config->systemPrompt
                ),
                ...$request->context,
                GptContextManager::createContextItem(
                    GptRolesEnum::USER, $request->message
                ),
            ],
        );

        $requestBody = [
            'modelUri' => 'gpt://' . $this->config->folderId . '/' . $this->config->modelName,
            'completionOptions' => [
                'stream' => $this->config->stream,
                'temperature' => $this->config->temperature,
                'maxTokens' => $this->config->maxTokens,
            ],
            'messages' => $messages,
        ];

        try {
            $response = $httpClient->post('', [
                'json' => $requestBody,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            $message = $responseData['result']['alternatives'][0]['message']['text']
                ?? 'Сбой при получении сообщения от менеджера';

            return new HandlerResponseDto(
                result: $message,
                addToContext: [],
                status: HandlerResponseStatusEnum::FINAL
            );
        } catch (GuzzleException $e) {
            return new HandlerResponseDto(
                result: 'Ошибка: ' . $e->getMessage(),
                addToContext: [],
                status: HandlerResponseStatusEnum::ERROR
            );
        }
    }

    /**
     * Устанавливает приоритет обработчика.
     *
     * @param int $priority Приоритет обработчика.
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Возвращает приоритет обработчика.
     *
     * @return int Приоритет обработчика.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Получает признак использования обработчика.
     *
     * @return HandlerUsageEnum Признак использования обработчика.
     */
    public static function getHandlerUsage(): HandlerUsageEnum
    {
        return HandlerUsageEnum::PAID_MODEL_GPT;
    }

    /**
     * Возвращает HTTP клиент.
     *
     * @return Client HTTP клиент.
     */
    private function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'base_uri' => $this->config->url,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->bearerToken,
                    'x-folder-id' => $this->config->folderId,
                    'Content-Type' => 'application/json',
                ],
                'verify' => true,
            ]);
        }

        return $this->httpClient;
    }
}
