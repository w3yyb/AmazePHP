<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;


use AmazePHP\Router as RouterClass;
use AmazePHP\Facade;
 
class Router extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return  RouterClass::class;
    }
}
