<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Interface CollectionInterface
 * Определяет методы для работы с коллекциями объектов.
 * Также позволяет доступ к элементам коллекции как к массиву.
 */
interface CollectionInterface extends \ArrayAccess, \Iterator
{
    /**
     * Возвращает коллекцию в виде массива.
     *
     * @return array
     */
    public function toArray(): array;
}
