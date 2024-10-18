<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\SystemCommandHandler\Handlers\Dto;

use App\Services\BotService\Interfaces\ConfigDtoInterface;

/**
 * Class GetPriceListConfigDto
 * DTO для конфигурации обработчика получения прайс-листа.
 */
readonly class GetPriceListConfigDto implements ConfigDtoInterface
{
    /**
     * @param string $priceListUrl URL для получения прайс-листа.
     */
    public function __construct(
        public string $priceListUrl,
    ) {}
}
