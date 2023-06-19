<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\Session as SessionClass;
use AmazePHP\Facade;
 
class Session extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return SessionClass::class;
    }
}
