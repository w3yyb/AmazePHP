<?php
 
namespace AmazePHP;


/**
 * Facade
 */
class Facade
{
    /**
     * Always create a new object instance
     * @var bool
     */
    protected static $alwaysNewInstance;

    /**
     * Create a Facade instance
     * @static
     * @access protected
     * @param  string $class       Class name or identifier
     * @param  array  $args        variable
     * @param  bool   $newInstance Whether to create a new instance each time
     * @return object
     */
    protected static function createFacade(string $class = '', array $args = [], bool $newInstance = false)
    {
        $class = $class ?: static::class;

        $facadeClass = static::getFacadeClass();

        if ($facadeClass) {
            $class = $facadeClass;
        }

        if (static::$alwaysNewInstance) {
            $newInstance = true;
        }

        return  $class;
        // return Container::getInstance()->make($class, $args, $newInstance);
    }

    /**
     * Gets the current Facade corresponding class name
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {}

    /**
     * Instantiates the current Facade class with parameters
     * @access public
     * @return object
     */
    public static function instance(...$args)
    {
        if (__CLASS__ != static::class) {
            return self::createFacade('', $args);
        }
    }

    /**
     * Call an instance of the class
     * @access public
     * @param  string     $class       Class name or identifier
     * @param  array|true $args        variable
     * @param  bool       $newInstance Whether to create a new instance each time
     * @return object
     */
    public static function make(string $class, $args = [], $newInstance = false)
    {
        if (__CLASS__ != static::class) {
            return self::__callStatic('make', func_get_args());
        }

        if (true === $args) {
            // New instantiated objects are always created
            $newInstance = true;
            $args        = [];
        }

        return self::createFacade($class, $args, $newInstance);
    }

    // Call the methods of the actual class
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}
