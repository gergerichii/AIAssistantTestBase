<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Handlers;

use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\RequestHandler\Dto\RequestDto;
use App\Services\BotService\Core\RequestHandler\Dto\ResponseDto;
use App\Services\BotService\Core\RequestHandler\Enum\ResponseStatusEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\YandexGptConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Core\RequestHandler\Interfaces\HandlerInterface;
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
     * @param RequestDto $request Запрос для обработки.
     * @param RequestDto $userRequest Исходный запрос, поступивший на вход конвейера.
     * @return ResponseDto Ответ после обработки запроса.
     * @throws JsonException
     */
    public function handle(RequestDto $request, RequestDto $userRequest): ResponseDto
    {
        $httpClient = $this->getHttpClient();

        $messages = array_map(
            static fn($contextItem) => [
                'role' => $contextItem['role']->value,
                'text' => $contextItem['text'],
            ],
            [
                ContextManager::createContextItem(
                    GptRolesEnum::SYSTEM, $this->config->systemPrompt
                ),
                ...$request->context,
                ContextManager::createContextItem(
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

            return new ResponseDto(
                result: $message,
                addToContext: [],
                status: ResponseStatusEnum::FINAL
            );
        } catch (GuzzleException $e) {
            return new ResponseDto(
                result: 'Ошибка: ' . $e->getMessage(),
                addToContext: [],
                status: ResponseStatusEnum::ERROR
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
