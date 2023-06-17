<?php
use AmazePHP\Router;
use AmazePHP\VerifyCsrfToken;
define('BASE_PATH', __DIR__);
include 'AmazePHP/src/DotEnv.php';
include 'AmazePHP/src/ErrorHandel.php';
(new AmazePHP\DotEnv());
(new AmazePHP\ErrorHandel());

include 'vendor/autoload.php';

$router = Router::getInstance();
foreach (config('route') as $key => $value) {
    $router->addRoute($value[0][0], $value[1], $value[2], $value[3] ?? null, $value['middleware'] ?? []);
}

$request = AmazePHP\Request::getInstance();


$middleware =require BASE_PATH.'/config/middleware.php';
 
return    (new AmazePHP\Pipeline())->through($middleware)
->then($request ,function () use ($router) {
    $response =$router->doRouting();
    if ($response !== null) {
        echo $response;
    }
});
