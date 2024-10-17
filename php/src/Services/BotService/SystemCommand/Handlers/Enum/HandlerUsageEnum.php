<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Handlers\Enum;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

/**
 * Enum HandlerUsageEnum
 * Определяет признаки использования обработчиков системных команд.
 */
enum HandlerUsageEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case INTERNAL_SERVICE = 'internal_service';
    case EXTERNAL_SERVICE = 'external_service';

    /**
     * Возвращает название для конкретного признака использования обработчика.
     *
     * @return string Название признака использования.
     */
    public function getName(): string
    {
        return match($this) {
            self::INTERNAL_SERVICE => 'Внутренний сервис',
            self::EXTERNAL_SERVICE => 'Внешний сервис',
        };
    }
}
