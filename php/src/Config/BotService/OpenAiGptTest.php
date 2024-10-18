<?php

declare(strict_types=1);

use App\Services\BotService\Core\RequestHandler\Dto\ConfigDto as RequestHandlerConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\FirstMessageConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\OpenAIGptConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\HandshakeHandler;
use App\Services\BotService\Core\RequestHandler\Handlers\OpenAIGptHandler;
use App\Services\BotService\Dto\ConfigDto as BotConfigDto;

return new BotConfigDto(
    id: 'OpenAIGptTest',
    name: 'OpenAI Gpt Test',
    requestHandlers: [
        'firstMessage' => new RequestHandlerConfigDto(
            class: HandshakeHandler::class,
            usage: HandlerUsageEnum::STATIC_HANDLER, // Измените на соответствующий тип использования
            config: new FirstMessageConfigDto(
                welcomeMessage: 'Работа с OpenAI GPT пока не реализована.',
            ),
        ),
        'gptApi' => new RequestHandlerConfigDto(
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