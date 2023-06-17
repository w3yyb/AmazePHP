<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

use AmazePHP\Facade;
use eftec\bladeone\BladeOne;
 
class View extends Facade
{
    /**
     *
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
