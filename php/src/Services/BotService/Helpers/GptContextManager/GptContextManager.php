<?php

declare(strict_types=1);

namespace App\Services\BotService\Helpers\GptContextManager;

use App\Services\BotService\Handlers\Enums\GptRolesEnum;
use JsonException;

/**
 * Class GptContextManager
 * Менеджер для управления контекстом, сохраняемым в системной папке temp.
 */
class GptContextManager
{
    /**
     * @var string Идентификатор контекста.
     */
    private string $contextId;

    /**
     * @var array<array{role: GptRolesEnum, text: string, data: ?array<string>}> Массив контекста.
     */
    private array $context;

    /**
     * Конструктор класса GptContextManager.
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
     * @return array<array{role: GptRolesEnum, text: string, data: ?array<string>}> Массив контекста.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Обновляет контекст, перезаписывая его новым массивом.
     *
     * @param array<array{role: GptRolesEnum, text: string, data: ?array<string>}> $newContext Новый массив контекста.
     */
    public function updateContext(array $newContext): void
    {
        foreach ($newContext as $item) {
            $this->validateContextItem($item);
        }

        $this->context = $newContext;
    }

    /**
     * Добавляет строку в контекст.
     *
     * @param GptRolesEnum $role Роль.
     * @param string $text Текст.
     * @param array<string>|null $data Данные.
     */
    public function addContextItem(GptRolesEnum $role, string $text, ?array $data = null): void
    {
        $this->context[] = self::createContextItem(role: $role, text: $text, data: $data);
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
     * @return array<array{role: GptRolesEnum, text: string, data: ?array<string>}> Массив контекста.
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

        foreach ($context as $item) {
            if (!$this->isValidContextItem($item)) {
                return [];
            }
        }

        return array_map(
            fn($item) => [
                'role' => GptRolesEnum::from($item['role']),
                'text' => $item['text'],
                'data' => $item['data'] ?? null,
            ],
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
            array_map(
                static function ($item) {
                    $role = $item['role'] instanceof GptRolesEnum
                        ? $item['role']->value
                        : $item['role'];

                    return [
                        'role' => $role,
                        'text' => $item['text'],
                        'data' => $item['data'],
                    ];
                },
                $this->context
            ),
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

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
     * Деструктор класса GptContextManager.
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
     * Проверяет, является ли элемент контекста валидным.
     *
     * @param mixed $item Элемент контекста.
     * @return bool
     */
    private function isValidContextItem(mixed $item): bool
    {
        return is_array($item)
            && isset($item['role'], $item['text'])
            && is_string($item['role'])
            && is_string($item['text'])
            && (is_null($item['data']) || is_array($item['data']));
    }

    /**
     * Валидирует элемент контекста.
     *
     * @param array{role: GptRolesEnum, text: string, data: ?array<string>} $item Элемент контекста.
     */
    private function validateContextItem(array $item): void
    {
        if (!($item['role'] instanceof GptRolesEnum)
            || !is_string($item['text'])
            || !(is_null($item['data']) || is_array($item['data']))
        ) {
            throw new \InvalidArgumentException('Invalid context item structure.');
        }
    }

    /**
     * Создает элемент контекста.
     *
     * @param GptRolesEnum $role Роль.
     * @param string $text Текст.
     * @param array<string>|null $data Данные.
     * @return array{role: GptRolesEnum, text: string, data: ?array<string>} Элемент контекста.
     */
    public static function createContextItem(GptRolesEnum $role, string $text, ?array $data = null): array
    {
        return [
            'role' => $role,
            'text' => $text,
            'data' => $data,
        ];
    }
}
