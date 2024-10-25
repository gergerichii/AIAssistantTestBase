<?php

declare(strict_types=1);

namespace App;

use DI\Bridge\Slim\Bridge;
use Exception;
use Middlewares\Debugbar;
use Middlewares\Whoops;
use App\Config\RouteConfigurator;
use App\Config\ContainerConfigurator;
use Throwable;

/**
 * Основной класс приложения.
 */
class App
{
    /**
     * Запускает приложение.
     * @throws Exception
     */
    public function run(): void
    {
        $app = Bridge::create(ContainerConfigurator::createContainer());

        $app->add(Debugbar::class);
        $app->add(Whoops::class);

        // Создаем экземпляр конфигуратора маршрутов
        $routeConfigurator = new RouteConfigurator();
        $routeConfigurator->configure($app);

        try {
            $app->run();
        } catch (Throwable $e) {
            http_response_code(500);
        }
    }
}
