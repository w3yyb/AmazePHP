<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\Cache as CacheClass;
use AmazePHP\Facade;

 
class Cache extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return  CacheClass::getInstance();
    }
}
