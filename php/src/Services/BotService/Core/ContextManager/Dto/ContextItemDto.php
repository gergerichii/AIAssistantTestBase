<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\ContextManager\Dto;

use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;

/**
 * Class ContextItemDto
 * DTO для элемента контекста.
 */
class ContextItemDto
{
    /**
     * @param GptRolesEnum $role Роль.
     * @param string $text Текст.
     * @param array<string>|null $data Данные.
     */
    public function __construct(
        public readonly GptRolesEnum $role,
        public readonly string $text,
        public readonly ?array $data = null,
    ) {}

}
