<?php

declare(strict_types=1);

namespace App;

use Middlewares\AuraSession;
use Middlewares\Debugbar;
use Middlewares\Whoops;
use Slim\Factory\AppFactory;
use App\Config\RouteConfigurator;
use Throwable;

class App
{
    public function run(): void
    {
        define('CONFIG_DIR', __DIR__ . '/config/');

        $app = AppFactory::create();

        $app->add(Debugbar::class);
        $app->add(Whoops::class);
        $app->add(AuraSession::class);

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
