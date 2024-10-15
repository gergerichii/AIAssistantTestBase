<?php

declare(strict_types=1);

namespace App\Services\StoredConfig\Dto;

use App\Interfaces\CollectionInterface;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * Class ConfigDtoCollection
 * Коллекция для работы с DTO конфигураций, реализующих ConfigDtoInterface.
 */
class ConfigDtoCollection implements CollectionInterface
{
    /**
     * @var ConfigDtoInterface[] Массив элементов коллекции.
     */
    private array $items;

    /**
     * Конструктор коллекции ConfigDtoCollection.
     *
     * @param ConfigDtoInterface[] $items Массив элементов для инициализации коллекции.
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof ConfigDtoInterface) {
                throw new InvalidArgumentException(
                    'Тип элемента должен быть ' . ConfigDtoInterface::class
                );
            }
        }

        $this->items = $items;
    }

    /**
     * Проверяет, существует ли элемент с заданным смещением.
     *
     * @param mixed $offset Смещение для проверки.
     * @return bool Возвращает true, если элемент существует, иначе false.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Возвращает элемент по заданному смещению.
     *
     * @param mixed $offset Смещение элемента.
     * @return ConfigDtoInterface Элемент коллекции.
     */
    public function offsetGet(mixed $offset): ConfigDtoInterface
    {
        return $this->items[$offset];
    }

    /**
     * Устанавливает элемент по заданному смещению.
     *
     * @param mixed $offset Смещение элемента.
     * @param mixed $value Значение элемента.
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Коллекция является только для чтения.');
    }

    /**
     * Удаляет элемент по заданному смещению.
     *
     * @param mixed $offset Смещение элемента.
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Коллекция является только для чтения.');
    }

    /**
     * Возвращает текущий элемент.
     *
     * @return false|ConfigDtoInterface Текущий элемент.
     */
    public function current(): false|ConfigDtoInterface
    {
        return current($this->items);
    }

    /**
     * Переходит к следующему элементу.
     *
     * @return void
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * Возвращает ключ текущего элемента.
     *
     * @return string|int|null Ключ текущего элемента.
     */
    public function key(): string|int|null
    {
        return key($this->items);
    }

    /**
     * Проверяет, существует ли текущий элемент.
     *
     * @return bool Возвращает true, если текущий элемент существует, иначе false.
     */
    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    /**
     * Сбрасывает итератор на первый элемент.
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * Возвращает коллекцию в виде массива.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
