<?php

declare(strict_types=1);

namespace App\Services\StoredConfig\Interfaces;

/**
 * Интерфейс для провайдеров конфигурации.
 * Обычно имплементируется для модулей, которые работают с конфигами.
 */
interface ConfigProviderInterface
{
    /**
     * Возвращает конфигурацию для данного модуля.
     *
     * @return ConfigDtoInterface Объект конфигурации модуля.
     */
    public function getConfig(): ConfigDtoInterface;

    /**
     * Возвращает уникальный идентификатор модуля.
     *
     * @return string Уникальная метка модуля.
     */
    public function getModuleIdentifier(): string;

    public function setConfig(ConfigDtoInterface $newConfig): void;
}
