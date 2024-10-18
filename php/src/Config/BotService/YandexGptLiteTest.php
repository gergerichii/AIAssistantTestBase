<?php

declare(strict_types=1);

use App\Services\BotService\Core\RequestHandler\Dto\ConfigDto as RequestHandlerConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\FirstMessageConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Dto\YandexGptConfigDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\HandlerUsageEnum;
use App\Services\BotService\Core\RequestHandler\Handlers\HandshakeHandler;
use App\Services\BotService\Core\RequestHandler\Handlers\YandexGptHandler;
use App\Services\BotService\Core\SystemCommandHandler\Dto\ConfigDto as SystemCommandHandlerConfigDto;
use App\Services\BotService\Core\SystemCommandHandler\Handlers\Dto\GetPriceListConfigDto;
use App\Services\BotService\Core\SystemCommandHandler\Handlers\Enum\HandlerUsageEnum as SystemCommandHandlerUsageEnum;
use App\Services\BotService\Core\SystemCommandHandler\Handlers\GetPriceListHandler;
use App\Services\BotService\Dto\ConfigDto as BotConfigDto;

return new BotConfigDto(
    id: 'YandexGptLiteTest',
    name: 'Yandex Gpt Lite Test',
    requestHandlers: [
        'firstMessage' => new RequestHandlerConfigDto(
            class: HandshakeHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new FirstMessageConfigDto(
                welcomeMessage: 'Здравствуйте! Меня зовут Василий. Чем могу помочь?',
            ),
        ),
        'gptApi' => new RequestHandlerConfigDto(
            class: YandexGptHandler::class,
            usage: HandlerUsageEnum::PAID_MODEL_GPT,
            config: new YandexGptConfigDto(
                url: 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion',
                authKey: 'Api-Key AQVN0JPOwEXOjVAQozWe2U7XHro6hf4BIZA9A7DR',
//                authKey: 'Bearer t1.9euelZqJl4mbismXnJqTnJCUkJLOyO3rnpWaloyVlpzGj4qNnMacysfIicnl9PdzJTxH-e9yCGy33fT3M1Q5R_nvcghst83n9euelZrPkZCLj8eMmouNisabnoyOx-_8xeuelZrPkZCLj8eMmouNisabnoyOxw.vooVtiFFhzratEYDEzj52i_MpnDQrAVEHeqkzeZ1afuBKiE0o_T2k5NQhfO6XajSHTuJqEmb3VKiCIZXxH06CA',
                folderId: 'b1go54pps8fofr8ebicn',
                modelName: 'yandexgpt-lite/latest',
                maxTokens: 60,
                temperature: '0.7',
                stream: false,
                systemPrompt: "Тебя зовут Василий. Ты м��неджер компании 'Гранит Строй Реставрация'. Ты отвечаешь всегда коротко и по делу. Если ты не можешь ответить на запрос пользователя, в случае специфической информации, то ты отправляешь вместо человеческого сообщения системную команду, которую обработает система компании и в ответ предоставит тебе необходимую информацию. При необходимости предоставить прайс лист клиенту, в ответ на сообщение клиента ты отправляешь системную команду @get_price_list@ без человеческого текста! Если понимаешь что клиенту необходим менеджер, то в ответ на сообщение клиента ты отправляешь системную команду @call_manager@. Если клиент интересуется наличием товара, то ты отправляешь системную команду @check_stock|<Название товара>@ Обрати внимание что системную команду клиен�� не увидит!!! Обрати внимание что каждое сообщение от пользователя которое начинается с префикса 'bot: ' ты расцениваешь как ответ на твою системную команду (т.е. системное сообщение), и используешь данные после префикса для ответа клиенту (т.е. отвечаешь клиенту на предыдущий вопрос используя информацию из системного сообщения), либо исполняешь указания из такого сообщения!. Если пользователь задает вопросы не касающиеся сферы деятельности компании (гранит и гранитные изделия) то тогда отвечай: Извините, я не могу помочь вам с этим вопросам!",
            ),
        ),
    ],
    systemCommandHandlers: [
        'getPriceList' => new SystemCommandHandlerConfigDto(
            class: GetPriceListHandler::class,
            usage: SystemCommandHandlerUsageEnum::INTERNAL_SERVICE,
            config: new GetPriceListConfigDto(
                priceListUrl: 'https://example.com/api/price-list',
            ),
        ),
    ],
);
