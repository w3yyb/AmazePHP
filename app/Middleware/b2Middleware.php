<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;

class b2Middleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {
        // $object->runs[] = 'before';
        echo ' <b>before222</b> ';

        return $next($object);
    }

}
?>
