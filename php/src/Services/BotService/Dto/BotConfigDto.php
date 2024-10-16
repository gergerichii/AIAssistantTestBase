<?php

declare(strict_types=1);

namespace App\Services\BotService\Dto;

readonly class BotConfigDto
{
    /**
     * @param string $id
     * @param string $name
     * @param BotHandlerConfigDto[] $handlers
     */
    public function __construct(
        public string $id,
        public string $name,
        public array $handlers,
    ){
    }
}