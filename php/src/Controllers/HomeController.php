<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Контроллер для обработки корневой страницы
 */
class HomeController
{
    /**
     * Обрабатывает запрос к корневой странице
     *
     * @param Request $request Запрос
     * @param Response $response Ответ
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $response->getBody()->write('Добро пожаловать на главную страницу!');

        return $response->withHeader('Content-Type', 'text/html');
    }
}
