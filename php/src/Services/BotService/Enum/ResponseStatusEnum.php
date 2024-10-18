<?php

declare(strict_types=1);

namespace App\Services\BotService\Enum;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

/**
 * Enum ResponseStatus
 * Определяет возможные статусы ответа.
 */
enum ResponseStatusEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case OK = 'ok';
    case ERROR = 'error';

    /**
     * Возвращает название для конкретного статуса.
     *
     * @return string Название статуса.
     */
    public function getName(): string
    {
        return match($this) {
            self::OK => 'Успешно',
            self::ERROR => 'Ошибка',
        };
    }
}
