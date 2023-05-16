<?php
define('BASE_PATH', __DIR__);

include 'vendor/autoload.php';
(new DotEnv());
(new ErrorHandel());
$REQUEST= empty($_REQUEST) ?    file_get_contents("php://input"): $_REQUEST;

$router = Router::getInstance();
foreach (config('route') as $key => $value) {
        $router->addRoute($value[0][0], $value[1], $value[2], $value[3] ?? null);
}

$response =$router->doRouting();

if ($response !== null) {
        echo $response;
}
