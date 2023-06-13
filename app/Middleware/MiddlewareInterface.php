<?php
namespace App\Middleware;

use \Closure as Closure;

interface MiddlewareInterface
{
    public function process($object, Closure $next, $params);
}
