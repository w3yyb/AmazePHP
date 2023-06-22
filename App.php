<?php
use Illuminate\Container\Container;
use AmazePHP\Router;
use AmazePHP\LoadConfiguration;

define('BASE_PATH', __DIR__);
define('APP_VERSION', '2.3.1');
include 'AmazePHP/src/DotEnv.php';
include 'AmazePHP/src/ErrorHandel.php';
(new AmazePHP\DotEnv());
(new AmazePHP\ErrorHandel());

include 'vendor/autoload.php';
$container = Container::getInstance();

$container->singleton('Router', 'Router');
$container->singleton('AmazePHP\LoadConfiguration', 'AmazePHP\LoadConfiguration');
$container->singleton('AmazePHP\DB', 'AmazePHP\DB');
$container->singleton('AmazePHP\Cache', 'AmazePHP\Cache');
$container->singleton('AmazePHP\Request', 'AmazePHP\Request');
$container->singleton('Request', 'AmazePHP\Request');

$container->make(LoadConfiguration::class);
$router = $container->make(Router::class);

foreach (config('route') as $key => $value) {
    $router->addRoute($value[0][0], $value[1], $value[2], $value[3] ?? null, $value['middleware'] ?? []);
}

$request = $container->make('Request');
$middleware =require BASE_PATH.'/config/middleware.php';

return (new AmazePHP\Pipeline())->through($middleware)
->then($request, function () use ($router) {
    $response =$router->doRouting();
    if ($response !== null) {
        echo $response;
    }
});
