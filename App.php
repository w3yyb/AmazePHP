<?php

include 'vendor/autoload.php';
(new DotEnv());
$REQUEST= empty($_REQUEST) ?    file_get_contents("php://input"): $_REQUEST;

try {
    $router = new Router();
    foreach (config('route') as $key => $value) {
        $router->addRoute($value[0][0], $value[1], $value[2]);
    }

    $router->doRouting();
} catch (Throwable $e) {
    http_response_code(404);
    include '404.html';
}
