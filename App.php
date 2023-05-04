<?php

include 'vendor/autoload.php';
(new DotEnv());
(new ErrorHandel());
$REQUEST= empty($_REQUEST) ?    file_get_contents("php://input"): $_REQUEST;

$router = new Router();
foreach (config('route') as $key => $value) {
        $router->addRoute($value[0][0], $value[1], $value[2]);
}

$router->doRouting();
