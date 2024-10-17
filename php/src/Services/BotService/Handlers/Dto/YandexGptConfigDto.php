<?php

declare(strict_types=1);

namespace App\Services\BotService\Handlers\Dto;

use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

/**
 * Class YandexGptDto
 * 
 * DTO для хранения настроек Yandex GPT.
 */
readonly class YandexGptConfigDto implements ConfigDtoInterface
{
    /**
     * DTO для хранения настроек Yandex GPT.
     * @param string $url URL Yandex GPT API
     * @param string $bearerToken API key Yandex Cloud
     * @param string $folderId Folder ID Yandex Cloud
     * @param string $modelName URI модели Yandex GPT
     * @param int $maxTokens Максимальное количество токенов ответа
     * @param string $temperature Температура ответа (от 0 до 1)
     * @param bool $stream Флаг использования стриминга
     */
    public function __construct(
        public string $url,
        public string $bearerToken,
        public string $folderId,
        public string $modelName,
        public int $maxTokens,
        public string $temperature,
        public bool $stream,
        public string $systemPrompt,
    ) {}
}