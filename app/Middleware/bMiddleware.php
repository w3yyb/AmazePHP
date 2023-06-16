<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;


class bMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {
        // $object->runs[] = 'before';
        echo ' <b>before</b> ';

        return $next($object);
    }

}
?>
