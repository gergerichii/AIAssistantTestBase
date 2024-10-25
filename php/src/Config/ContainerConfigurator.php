<?php

declare(strict_types=1);

namespace App\Config;

use App\Interfaces\UserInterface;
use App\Models\User;
use App\Services\BotService\Core\ContextManager\ContextManager;
use App\Services\BotService\Core\ContextManager\Interfaces\ContextManagerInterface;
use App\Services\BotService\Core\ContextManager\Interfaces\ContextStorageDriverInterface;
use App\Services\BotService\Core\ContextManager\StorageDrivers\FileSystemContextStorageDriver;
use App\Services\BotService\Helpers\ConfigManager\Drivers\PhpFileDriver;
use App\Services\BotService\Helpers\ConfigManager\Interfaces\DriverInterface as ConfMngrStrgDrvInterface;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Cookies;
use Slim\Psr7\Headers;

use function DI\factory;
use function DI\get;
use function DI\value;
use function DI\env;
use function DI\string;
use function DI\autowire;

/**
 * Класс для настройки контейнера зависимостей.
 */
class ContainerConfigurator
{
    /**
     * Создает и настраивает контейнер зависимостей.
     *
     * @return ContainerInterface Настроенный контейнер.
     * @throws Exception
     */
    public static function createContainer(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([
            //Aura Session
            Session::class => static function (): Session {
                $cookies = Headers::createFromGlobals()->getHeader('Cookie', []);

                return (new SessionFactory())->newInstance(Cookies::parseHeader($cookies));
            },
        ]);

        return $containerBuilder->build();
    }
}
