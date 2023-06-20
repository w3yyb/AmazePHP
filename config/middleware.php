<?php

return [
    AmazePHP\Middleware\Cors::class,
    AmazePHP\Middleware\StartSession::class,
    AmazePHP\Middleware\VerifyCsrfToken::class,
];
