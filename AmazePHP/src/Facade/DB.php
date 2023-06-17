<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\DB as DBClass;
use AmazePHP\Facade;

 
class DB extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return  DBClass::getInstance();
    }
}
