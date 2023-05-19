<?php

define('BASE_PATH', __DIR__);
include 'lib/DotEnv.php';
include 'lib/ErrorHandel.php';
(new DotEnv());
(new ErrorHandel());

include 'vendor/autoload.php';

$reqMet = $_SERVER['REQUEST_METHOD'];

if (config('session.enable')) {
    getSession();
    cookie('XSRF-TOKEN', session()->token(), config('session.lifetime'), config('session.path'), config('session.domain'), config('session.secure'), false, config('session.same_site'));
}


if (config('session.enable') && $reqMet=='POST'  || $reqMet=='PUT' || $reqMet=='DELETE' || $reqMet=='PATCH') {
    VerifyCsrfToken::getInstance();
}

$router = Router::getInstance();
foreach (config('route') as $key => $value) {
    $router->addRoute($value[0][0], $value[1], $value[2], $value[3] ?? null);
}

$response =$router->doRouting();

if ($response !== null) {
    echo $response;
}
