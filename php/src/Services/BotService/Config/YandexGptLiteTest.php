<?php

declare(strict_types=1);

use App\Services\BotService\Dto\BotConfigDto;
use App\Services\BotService\Dto\BotHandlerConfigDto;
use App\Services\BotService\Handlers\Dto\YandexGptConfigDto;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Handlers\YandexGptMessageHandler;

return new BotConfigDto(
    id: 'YandexGptLiteTest',
    name: 'Yandex Gpt Lite Test',
    handlers: [
        'gptApi' => new BotHandlerConfigDto(
            class: YandexGptMessageHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new YandexGptConfigDto(
                url: 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion',
                apiKey: 'AQVNyRrW09cm9l1wGh4vECK5RlUGFpi3s4t9n-LS',
                folderId: 'b1go54pps8fofr8ebicn',
                modelUri: 'gpt://b1go54pps8fofr8ebicn/yandexgpt-lite/latest',
                maxTokens: 2000,
                temperature: '0.6',
                stream: false,
            ),
        ),
    ],
);