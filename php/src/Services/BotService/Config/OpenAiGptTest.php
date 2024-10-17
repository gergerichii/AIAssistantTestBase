<?php

declare(strict_types=1);

use App\Services\BotService\Dto\BotConfigDto;
use App\Services\BotService\Request\Dto\HandlerConfigDto;
use App\Services\BotService\Request\Handlers\Dto\FirstMessageConfigDto;
use App\Services\BotService\Request\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Request\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Request\Handlers\HandshakeHandler;
use App\Services\BotService\Request\Handlers\OpenAIGptHandler;

return new BotConfigDto(
    id: 'OpenAIGptTest',
    name: 'OpenAI Gpt Test',
    handlers: [
        'firstMessage' => new HandlerConfigDto(
            class: HandshakeHandler::class,
            usage: HandlerUsageEnum::STATIC_HANDLER, // Измените на соответствующий тип использования
            config: new FirstMessageConfigDto(
                welcomeMessage: 'Работа с OpenAI GPT пока не реализована.',
            ),
        ),
        'gptApi' => new HandlerConfigDto(
            class: OpenAIGptHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new OpenAIGptConfigDto(
                url: '',
                apiKey: '',
                modelName: '',
            ),
        )
    ],
);