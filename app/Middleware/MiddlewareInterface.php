<?php
namespace App\Middleware;

use \Closure as Closure;

interface MiddlewareInterface
{
    //return Response
    public function process($request, Closure $next, $params);
}
