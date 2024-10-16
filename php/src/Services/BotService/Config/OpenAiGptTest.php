<?php

declare(strict_types=1);

use App\Services\BotService\Dto\BotConfigDto;
use App\Services\BotService\Dto\BotHandlerConfigDto;
use App\Services\BotService\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Handlers\OpenAIGptMessageHandler;

return new BotConfigDto(
    id: 'OpenAIGptTest',
    name: 'OpenAI Gpt Test',
    handlers: [
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