<?php

return [
    // App\Middleware\aMiddleware::class,
    // App\Middleware\bMiddleware::class,
    AmazePHP\Middleware\Cors::class,
   AmazePHP\Middleware\StartSession::class,

    AmazePHP\Middleware\VerifyCsrfToken::class,
];
?>
