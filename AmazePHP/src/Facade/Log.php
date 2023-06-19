<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\Log as LogClass;
use AmazePHP\Facade;
 
class Log extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return  LogClass::class;
    }
}
