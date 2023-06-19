<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\Cache as CacheClass;
use AmazePHP\Facade;

 
class Cache extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return   CacheClass::class;
    }
}
