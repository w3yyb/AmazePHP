<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\DB as DBClass;
use AmazePHP\Facade;

 
class DB extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return   DBClass::class;
    }
}
