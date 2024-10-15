<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Enums;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

/**
 * Enum ResponseStatus
 * Определяет возможные статусы ответа.
 */
enum ResponseStatusEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case FINAL = 'final';
    case INTERMEDIATE = 'intermediate';
    case NO_ANSWER = 'no_answer';

    /**
     * Возвращает название для конкретного статуса.
     *
     * @return string Название статуса.
     */
    public function getName(): string
    {
        return match($this) {
            self::FINAL => 'Окончательный',
            self::INTERMEDIATE => 'Промежуточный',
            self::NO_ANSWER => 'Нет ответа',
        };
    }
}
