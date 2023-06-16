<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;

class aMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {

        $response = $next($object);
        echo ' <b>after</b> ';

        return $response;
    }

}
?>
