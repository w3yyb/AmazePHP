<?php
namespace App\Middleware;

class bMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {
        // $object->runs[] = 'before';
        echo ' <b>before</b> ';

        return $next($object);
    }

}
?>
