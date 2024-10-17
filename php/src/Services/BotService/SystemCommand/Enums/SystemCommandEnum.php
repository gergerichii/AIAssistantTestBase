<?php

declare(strict_types=1);

namespace App\Services\BotService\SystemCommand\Enums;

use App\Interfaces\NamedEnumInterface;
use App\Traits\EnumTrait;

/**
 * Enum SystemCommandEnum
 * Определяет возможные системные команды.
 */
enum SystemCommandEnum: string implements NamedEnumInterface
{
    use EnumTrait;

    case GET_PRICE_LIST = 'get_price_list';
    case CALL_MANAGER = 'call_manager';
    case CHECK_STOCK = 'check_stock';

    /**
     * Возвращает название для конкретной команды.
     *
     * @return string Название команды.
     */
    public function getName(): string
    {
        return match($this) {
            self::GET_PRICE_LIST => 'Получить прайс-лист',
            self::CALL_MANAGER => 'Вызвать менеджера',
            self::CHECK_STOCK => 'Проверить наличие товара',
        };
    }
}
