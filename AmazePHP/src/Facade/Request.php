<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\Request as RequestClass;
use AmazePHP\Facade;

 
 
class Request extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return  RequestClass::getInstance();
    }
}
