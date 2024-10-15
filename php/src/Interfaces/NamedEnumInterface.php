<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Interface EnumNamesInterface
 * Определяет методы для получения названий для Enum.
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
interface NamedEnumInterface
{
    /**
     * Возвращает название для конкретного значения Enum.
     *
     * @return string Название Enum.
     */
    public function getName(): string;

    /**
     * Возвращает список всех названий для Enum.
     *
     * @return array Массив, где ключ - это значение Enum, а значение - название.
     */
    public static function getNames(): array;
}
