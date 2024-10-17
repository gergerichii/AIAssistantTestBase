<?php

declare(strict_types=1);

use App\Services\BotService\Dto\BotConfigDto;
use App\Services\BotService\Dto\BotHandlerConfigDto;
use App\Services\BotService\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Handlers\OpenAIGptMessageHandler;
use App\Services\BotService\Handlers\FirstMessageHandler;
use App\Services\BotService\Handlers\Dto\FirstMessageConfigDto;

return new BotConfigDto(
    id: 'OpenAIGptTest',
    name: 'OpenAI Gpt Test',
    handlers: [
        'firstMessage' => new BotHandlerConfigDto(
            class: FirstMessageHandler::class,
            usage: HandlerUsageEnum::STATIC_HANDLER, // Измените на соответствующий тип использования
            config: new FirstMessageConfigDto(
                welcomeMessage: 'Работа с OpenAI GPT пока не реализована.',
            ),
        ),
        'gptApi' => new BotHandlerConfigDto(
            class: OpenAIGptMessageHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new OpenAIGptConfigDto(
                url: '',
                apiKey: '',
                modelName: '',
            ),
        )
    ],
);