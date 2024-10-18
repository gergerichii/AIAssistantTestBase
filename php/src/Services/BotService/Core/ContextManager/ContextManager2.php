<?php

declare(strict_types=1);

namespace App\Services\BotService\Core\ContextManager;

use App\Services\BotService\Core\ContextManager\Dto\ContextItemDto;
use App\Services\BotService\Core\RequestHandler\Handlers\Enums\GptRolesEnum;
use JsonException;

/**
 * Class ContextManager2
 * Менеджер для управления контекстом, сохраняемым в системной папке temp.
 */
class ContextManager2
{
    /**
     * @var string Идентификатор контекста.
     */
    private string $contextId;

    /**
     * @var ContextItemDto[] Массив контекста.
     */
    private array $context;

    /**
     * Конструктор класса ContextManager.
     *
     * @param string $contextId Идентификатор контекста.
     * @throws JsonException
     */
    public function __construct(string $contextId)
    {
        $this->contextId = $contextId;
        $this->context = $this->loadContext();
    }

    /**
     * Деструктор класса ContextManager.
     * Сохраняет контекст на диск при разрушении объекта.
     */
    public function __destruct()
    {
        try {
            $this->saveContext();
        } catch (JsonException $e) {
            // Обработка исключения при сохранении контекста
        }
    }

    /**
     * Удаляет текущий контекст.
     */
    public function deleteContext(): void
    {
        $this->context = [];
    }

    /**
     * Добавляет элемент в контекст.
     *
     * @param ContextItemDto $item Элемент контекста.
     */
    public function addContextItem(ContextItemDto $item): void
    {
        $this->context[] = $item;
    }

    /**
     * Преобразует текущий контекст в массив.
     *
     * @return array Массив элементов контекста.
     */
    public function toArray(): array
    {
        return $this->mapContextItemsToArray();
    }

    /**
     * Загружает контекст из файла.
     *
     * @return ContextItemDto[] Массив контекста.
     * @throws JsonException
     */
    private function loadContext(): array
    {
        $filePath = $this->getFilePath();

        if (!file_exists($filePath)) {
            return [];
        }

        $data = file_get_contents($filePath);
        $context = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($context)) {
            return [];
        }

        return $this->mapContextItems($context);
    }

    /**
     * Преобразует массив данных контекста в массив DTO.
     *
     * @param array $context Массив данных контекста.
     * @return ContextItemDto[] Массив DTO элементов контекста.
     */
    private function mapContextItems(array $context): array
    {
        /** @var array{role: string, text: string, data: ?array<string>} $item */
        return array_map(
            static fn(array $item): ContextItemDto => new ContextItemDto(
                role: GptRolesEnum::from($item['role']),
                text: $item['text'],
                data: $item['data'] ?? null,
            ),
            $context
        );
    }

    /**
     * Сохраняет текущий контекст в файл.
     *
     * @throws JsonException
     */
    private function saveContext(): void
    {
        $filePath = $this->getFilePath();
        $data = json_encode(
            $this->mapContextItemsToArray(),
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        file_put_contents($filePath, $data);
    }

    /**
     * Преобразует массив DTO в массив данных.
     *
     * @return array Массив данных элементов контекста.
     */
    private function mapContextItemsToArray(): array
    {
        return array_map(
            static fn(ContextItemDto $item): array => [
                'role' => $item->role->value,
                'text' => $item->text,
                'data' => $item->data,
            ],
            $this->context
        );
    }

    /**
     * Возвращает путь к файлу контекста.
     *
     * @return string Путь к файлу.
     */
    private function getFilePath(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'context_' . $this->contextId . '.json';
    }
}
