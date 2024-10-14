<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Контроллер для обработки запросов к /chat_bot
 */
class ChatBotController
{
    /**
     * Обрабатывает POST запросы к /chat_bot
     *
     * @param Request $request HTTP запрос
     * @param Response $response HTTP ответ
     * @return Response
     * @throws \JsonException
     */
    public function handlePost(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        
        $userMessage = $body['message'] ?? '';

        if ($userMessage === '@handshake') {
            $botResponse = 'Здравствуйте! Меня зовут Василий! Я ваш личный менеджер. Какие у вас есть вопросы?';
        } else {
            $responses = ['Привет!', 'Как я могу помочь вам?', 'Рад познакомиться!', 'До свидания!'];
            $botResponse = $responses[array_rand($responses)];
        }

        $response->getBody()->write(json_encode(['reply' => $botResponse]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
