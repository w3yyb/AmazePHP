<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

use AmazePHP\Facade;
use eftec\bladeone\BladeOne;
 
class View extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        $views = BASE_PATH  . "/template";
        $cache = BASE_PATH. "/cache";
        $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        return $blade;
    }
}
