<?php
declare(strict_types=1);

use App\App;
use Symfony\Component\ErrorHandler\Error\FatalError;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();
try {
    $app->run();
} catch (Throwable $e) {
    http_response_code(500);
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
