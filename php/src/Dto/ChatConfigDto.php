<?php

declare(strict_types=1);

namespace App\Dto;

use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;

readonly class ChatConfigDto implements ConfigDtoInterface
{
    public function __construct(
        public string $currentGptModel
    ) {
    }
}