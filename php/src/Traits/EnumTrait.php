<?php

declare(strict_types=1);

namespace App\Traits;

use App\Interfaces\NamedEnumInterface;
use Illuminate\Support\Enumerable;

/**
 * Trait EnumTrait
 * Предоставляет методы для работы с Enum, включая получение названий.
 *
 * Пример использования:
 * ```
 * enum Status: string implements EnumNamesInterface
 * {
 *     use EnumTrait;
 *
 *     case Pending = 'pending';
 *     case Approved = 'approved';
 *     case Rejected = 'rejected';
 *
 *     public function getName(): string
 *     {
 *         return match($this) {
 *             self::Pending => 'Ожидается',
 *             self::Approved => 'Одобрено',
 *             self::Rejected => 'Отклонено',
 *         };
 *     }
 * }
 *
 * $status = Status::Approved;
 *
 * // Получение названия для конкретного кейса
 * echo $status->getName(); // Вывод: "Одобрено"
 *
 * // Получение всех названий
 * $names = Status::getNames();
 * print_r($names);
 * // Вывод:
 * // Array
 * // (
 * //     [Pending] => Ожидается
 * //     [Approved] => Одобрено
 * //     [Rejected] => Отклонено
 * // )
 * ```
 */
trait EnumTrait
{
    /**
     * Возвращает список всех названий для Enum.
     *
     * @return array Массив, где ключ - это имя Enum, а значение - название.
     */
    public static function getNames(): array
    {
        $cases = self::cases();
        $result = [];

        /** @var NamedEnumInterface|Enumerable $case */
        foreach ($cases as $case) {
            $result[$case->name] = $case->getName();
        }

        return $result;
    }
}
