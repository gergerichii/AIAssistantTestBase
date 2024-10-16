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
     *
     * @param string $url URL для доступа к Yandex GPT.
     * @param string $apiKey API ключ для аутентификации.
     */
    public function __construct(
        public string $url,
        public string $apiKey,
        public string $folderId,
        public string $modelUri,
        public int $maxTokens,
        public string $temperature,
        public bool $stream,
    ) {}
}