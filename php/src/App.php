<?php

declare(strict_types=1);

namespace App;

use Slim\Factory\AppFactory;
use App\Config\RouteConfigurator;

class App
{
    public function run(): void
    {
        $app = AppFactory::create();

        // Создаем экземпляр конфигуратора маршрутов
        $routeConfigurator = new RouteConfigurator();
        $routeConfigurator->configure($app);

        $app->run();
    }
}
