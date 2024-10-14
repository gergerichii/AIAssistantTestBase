<?php

declare(strict_types=1);

namespace App\Config;

use Slim\App;
use App\Controllers\InfoController;
use App\Controllers\HomeController;
use App\Controllers\ChatBotController; // Добавляем новый контроллер

/**
 * Класс для настройки маршрутов приложения
 */
class RouteConfigurator
{
    /**
     * Настраивает маршруты для приложения
     *
     * @param App $app Экземпляр приложения Slim
     */
    public function configure(App $app): void
    {
        // Добавляем маршрут для корневой страницы
        $app->get('/', [HomeController::class, 'index']);

        // Изменяем маршрут для phpinfo
        $app->get('/phpinfo', [InfoController::class, 'phpInfo']);

        // Добавляем маршрут для chat_bot
        $app->post('/chat_bot', [ChatBotController::class, 'handlePost']);
    }
}
