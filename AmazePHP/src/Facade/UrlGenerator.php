<?php
 
declare (strict_types = 1);

namespace AmazePHP\Facade;

 use AmazePHP\UrlGenerator as UrlGeneratorClass;
use AmazePHP\Facade;

 
class UrlGenerator extends Facade
{
    /**
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return   UrlGeneratorClass::class;
    }
}
