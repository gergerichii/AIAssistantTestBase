<?php

declare(strict_types=1);

namespace App\Services\BotService\Handlers\Enums;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

/**
 * Enum HandlerUsageEnum
 * Определяет признаки использования обработчика.
 */
enum HandlerUsageEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case PAID_MODEL_GPT = 'paid_model_gpt';
    case STATIC_HANDLER = 'static_handler';

    /**
     * Возвращает название для конкретного признака использования обработчика.
     *
     * @return string Название признака использования.
     */
    public function getName(): string
    {
        return match($this) {
            self::PAID_MODEL_GPT => 'Платная модель GPT',
            self::STATIC_HANDLER => 'Статический обработчик',
        };
    }
}
