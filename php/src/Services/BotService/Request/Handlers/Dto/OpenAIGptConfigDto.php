<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Handlers\Dto;

use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

/**
 * Class OpenAIGptDto
 * 
 * DTO для хранения настроек OpenAI GPT.
 */
readonly class OpenAIGptConfigDto implements ConfigDtoInterface
{
    /**
     * DTO для хранения настроек OpenAI GPT.
     *
     * @param string $url URL для доступа к OpenAI GPT.
     * @param string $apiKey API ключ для аутентификации.
     * @param string $modelName Название обученной модели OpenAI GPT.
     */
    public function __construct(
        public string $url,
        public string $apiKey,
        public string $modelName,
    ) {}
}