<?php

declare(strict_types=1);

namespace App\Services\BotService\Request\Handlers\Enums;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

enum GptRolesEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case SYSTEM = 'system';
    case ASSISTANT = 'assistant';
    case USER = 'user';

    /**
     * Возвращает название для конкретного признака использования обработчика.
     *
     * @return string Название признака использования.
     */
    public function getName(): string
    {
        return match($this) {
            self::SYSTEM => 'Система',
            self::ASSISTANT => 'Ассистент',
            self::USER => 'Пользователь',
        };
    }
}
