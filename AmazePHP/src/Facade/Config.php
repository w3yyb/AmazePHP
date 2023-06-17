<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;


use AmazePHP\LoadConfiguration;
use AmazePHP\Facade;

 
class Config extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return LoadConfiguration::getInstance();
    }
}
