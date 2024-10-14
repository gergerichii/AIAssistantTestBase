<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InfoController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function phpInfo(Request $request, Response $response): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        $response->getBody()->write($phpinfo);
        return $response;
    }
}
