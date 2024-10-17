<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

use App\Services\BotService\Request\Dto\HandlerConfigDto;

readonly class BotConfigDto
{
    /**
     * @param string $id
     * @param string $name
     * @param HandlerConfigDto[] $handlers
     */
    public function __construct(
        public string $id,
        public string $name,
        public array $handlers,
    ){
    }
}