<?php

declare(strict_types=1);

use App\Services\BotService\Dto\BotConfigDto;
use App\Services\BotService\Dto\BotHandlerConfigDto;
use App\Services\BotService\Handlers\Dto\YandexGptConfigDto;
use App\Services\BotService\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Handlers\YandexGptMessageHandler;
use App\Services\BotService\Handlers\FirstMessageHandler;
use App\Services\BotService\Handlers\Dto\FirstMessageConfigDto;

return new BotConfigDto(
    id: 'YandexGptLiteTest',
    name: 'Yandex Gpt Lite Test',
    handlers: [
        'firstMessage' => new BotHandlerConfigDto(
            class: FirstMessageHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new FirstMessageConfigDto(
                welcomeMessage: 'Здравствуйте! Меня зовут Василий. Чем могу помочь?',
            ),
        ),
        'gptApi' => new BotHandlerConfigDto(
            class: YandexGptMessageHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new YandexGptConfigDto(
                url: 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion',
                bearerToken: 't1.9euelZqJl4mbismXnJqTnJCUkJLOyO3rnpWaloyVlpzGj4qNnMacysfIicnl9PdzJTxH-e9yCGy33fT3M1Q5R_nvcghst83n9euelZrPkZCLj8eMmouNisabnoyOx-_8xeuelZrPkZCLj8eMmouNisabnoyOxw.vooVtiFFhzratEYDEzj52i_MpnDQrAVEHeqkzeZ1afuBKiE0o_T2k5NQhfO6XajSHTuJqEmb3VKiCIZXxH06CA',
                folderId: 'b1go54pps8fofr8ebicn',
                modelName: 'yandexgpt-lite/latest',
                maxTokens: 60,
                temperature: '0.7',
                stream: false,
                systemPrompt: "Тебя зовут Василий. Ты менеджер компании 'Гранит Строй Реставрация'. Ты отвечаешь всегда коротко и по делу. Если ты не можешь ответить на запрос пользователя, в случае специфической информации, то ты отправляешь вместо человеческого сообщения системную команду, которую обработает система компании и в ответ предоставит тебе необходимую информацию. При необходимости предоставить прайс лист клиенту, в ответ на сообщение клиента ты отправляешь системную команду @get_price_list@ без человеческого текста! Если понимаешь что клиенту необходим менеджер, то в ответ на сообщение клиента ты отправляешь системную команду @call_manager@. Обрати внимание что системную команду клиент не увидит!!! Обрати внимание что каждое сообщение от пользователя которое начинается с префикса 'bot: ' ты расцениваешь как ответ на твою системную команду (т.е. системное сообщение), и используешь данные после префикса для ответа клиенту (т.е. отвечаешь клиенту на предыдущий вопрос используя информацию из системного сообщения), либо исполняешь указания из такого сообщения!",
//                systemPrompt: "Тебя зовут Василий и ты разговариваешь всегда вежливым деловым языком. Ты менеджер в компании, торгующей изделиями из гранита. Ты отвечаешь всегда максимально коротко и по делу, но вежливо. Ты отвечаешь без форматирования текста и переноса строк. Ты здороваешься только в начале разговора. Если запрос начинается с префикса 'bot:' значит запрос сформирован автоматически и не виден пользователю и нужно в точности выполнить его поручение. В некоторых ситуациях ты отправляешь системные команды вместо человеческого ответа: При необходимости предоставить прайс лист клиенту ты отправляешь @get_price_list@. Если понимаешь что клиенту необходим менеджер, то отправляешь системную команду @call_manager@",
//                systemPrompt: "Тебя зовут Василий и ты разговариваешь всегда вежливым деловым языком. Ты менеджер в компании, торгующей изделиями из гранита. Ты отвечаешь всегда максимально коротко и по делу, но вежливо. Ты отвечаешь без форматирования текста и переноса строк. Ты здороваешься только в начале разговора. Если запрос начинается с префикса 'bot:', это означает, что запрос автоматически сгенерирован и не виден пользователю. Это ответ на твой системный запрос (системную команду отправленную тобой) Тебе нужно выполнить его точно. Когда необходимо отправить системную команду, например, `@get_price_list@`, ты отправляешь её как команду для выполнения, а не как часть ответа пользователю.: При необходимости предоставить прайс лист клиенту ты отправляешь @get_price_list@. Если понимаешь, что клиенту необходим менеджер, то отправляешь системную команду @call_manager@. При необходимости проверить наличие товара, отправляешь системную команду @check_stock@.",
            ),
        ),
    ],
);