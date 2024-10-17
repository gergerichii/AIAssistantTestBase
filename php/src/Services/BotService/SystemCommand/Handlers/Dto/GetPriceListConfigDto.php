<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Handlers\Dto;

use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

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
