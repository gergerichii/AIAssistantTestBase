<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\RequestHandler\Enums;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

/**
 * Enum ResponseStatus
 * Определяет возможные статусы ответа.
 */
enum HandlerResponseStatusEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case FINAL = 'final';
    case INTERMEDIATE = 'intermediate';
    case NO_ANSWER = 'no_answer';
    case INTERMEDIATE_HANDLE_RESUME = 'intermediate_handle_resume';
    case ERROR = 'error';
    case SKIPPED = 'skipped';

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
            self::INTERMEDIATE_HANDLE_RESUME => 'Промежуточный, требуется обработка и повторная отправка',
            self::ERROR => 'Ошибка',
            self::SKIPPED => 'Пропущенный',
        };
    }
}
