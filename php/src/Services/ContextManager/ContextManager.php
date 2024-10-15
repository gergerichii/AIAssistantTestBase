<?php

declare(strict_types=1);

namespace App\Services\ContextManager;

use JsonException;

/**
 * Class ContextManager
 * Менеджер для управления контекстом, сохраняемым в системной папке temp.
 */
class ContextManager
{
    /**
     * @var string Идентификатор контекста.
     */
    private string $contextId;

    /**
     * @var string[] Массив контекста.
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
     * Возвращает текущий контекст в виде массива.
     *
     * @return string[] Массив контекста.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Обновляет контекст, перезаписывая его новым массивом.
     *
     * @param string[] $newContext Новый массив контекста.
     */
    public function updateContext(array $newContext): void
    {
        $this->context = $newContext;
    }

    /**
     * Удаляет текущий контекст.
     */
    public function deleteContext(): void
    {
        $this->context = [];
    }

    /**
     * Загружает контекст из файла.
     *
     * @return string[] Массив контекста.
     * @throws JsonException
     */
    private function loadContext(): array
    {
        $filePath = $this->getFilePath();

        if (!file_exists($filePath)) {
            return [];
        }

        $data = file_get_contents($filePath);

        return json_decode($data, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    /**
     * Сохраняет текущий контекст в файл.
     *
     * @throws JsonException
     */
    private function saveContext(): void
    {
        $filePath = $this->getFilePath();
        $data = json_encode($this->context, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        file_put_contents($filePath, $data);
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
}
