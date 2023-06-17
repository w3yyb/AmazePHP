<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;


use AmazePHP\Router as RouterClass;
use AmazePHP\Facade;
 
class Router extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return  RouterClass::getInstance();
    }
}
