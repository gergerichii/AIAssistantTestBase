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
        $app->get('/', [HomeController::class, 'index']);

        $app->get('/phpinfo', [InfoController::class, 'phpInfo']);

        $app->post('/chat_bot', [ChatBotController::class, 'handlePostMessage']);
    }
}
