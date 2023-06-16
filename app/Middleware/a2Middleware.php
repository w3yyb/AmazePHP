<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;


class a2Middleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {

        $response = $next($object);
        echo ' <b>after222</b> ';

        return $response;
    }

}
?>
