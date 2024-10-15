<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService\Handlers\Enums;

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

    /**
     * Возвращает название для конкретного признака использования обработчика.
     *
     * @return string Название признака использования.
     */
    public function getName(): string
    {
        return match($this) {
            self::PAID_MODEL_GPT => 'Платная модель GPT',
        };
    }
}
