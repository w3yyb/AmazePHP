<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;


use AmazePHP\LoadConfiguration as LoadConfigurationClass;
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
        return  LoadConfigurationClass::class;
    }
}
