<?php

declare(strict_types=1);

namespace App\Services\StoredConfig\Storages;

use App\Services\StoredConfig\Interfaces\ConfigStorageInterface;
use App\Services\StoredConfig\Dto\ConfigDtoCollection;
use App\Services\StoredConfig\Interfaces\ConfigDtoInterface;
use JsonException;

/**
 * Class JsonConfigStorage
 *
 * Реализация интерфейса ConfigStorageInterface для хранения конфигурации в формате JSON.
 */
class JsonConfigStorage implements ConfigStorageInterface
{
    /**
     * Сохраняет конфигурацию в файл JSON.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param ConfigDtoCollection $config Коллекция объектов конфигурации.
     * @throws JsonException
     */
    public function save(string $configId, ConfigDtoCollection $config): void
    {
        $filePath = $this->getFilePath($configId);
        $data = [];

        /** @var ConfigDtoInterface $dto */
        foreach ($config as $key => $dto) {
            $data[$key] = [
                'class' => get_class($dto),
                'data' => get_object_vars($dto),
            ];
        }

        file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        );
    }

    /**
     * Загружает конфигурацию из файла JSON.
     *
     * @param string $configId Идентификатор конфигурации.
     * @param bool $clearCache Флаг загрузки конфигурации со сбросом кеша.
     * @return ConfigDtoCollection Объект конфигурации.
     * @throws JsonException
     */
    public function load(string $configId, bool $clearCache = false): ConfigDtoCollection
    {
        $filePath = $this->getFilePath($configId);

        if (!file_exists($filePath)) {
            return new ConfigDtoCollection([]);
        }

        try {
            $data = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            unlink($filePath);

            return new ConfigDtoCollection([]);
        }

        // Преобразование массива данных в коллекцию DTO
        $configs = [];
        try {
            foreach ($data as $key => $item) {
                $configs[$key] = new $item['class'](...$item['data']);
            }
        } catch (\Throwable $e) {
            unlink($filePath);

            return new ConfigDtoCollection([]);
        }

        return new ConfigDtoCollection($configs);
    }

    /**
     * Удаляет конфигурацию, удаляя соответствующий файл JSON.
     *
     * @param string $configId Идентификатор конфигурации.
     */
    public function delete(string $configId): void
    {
        $filePath = $this->getFilePath($configId);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Возвращает путь к файлу конфигурации.
     *
     * @param string $configId Идентификатор конфигурации.
     * @return string Путь к файлу.
     */
    private function getFilePath(string $configId): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $configId . '.json';
    }
}
