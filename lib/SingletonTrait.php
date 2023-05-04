<?php

/**
 * Singleton
 * class A: use SingletonTrait;
   * class B: A::getInstance();
 *
 */
trait SingletonTrait
{
    /**
        * @var Singleton reference to singleton instance
        */
    private static $instance=[];

    /**
     * gets the instance via lazy initialization (created on first usage).
     *
     * @return self
     */
    public static function getInstance()
    {
        $class = \get_called_class();
        $args  = \func_get_args();
        if (! isset(static::$instance[ $class ])) {
            static::$instance[ $class ] = new static(...$args);
        }
        return static::$instance[ $class ];
    }

    // private function __construct()

    protected function __construct()
    {
    }

    /**
     * Prevent the instance from being cloned.
     *
     * @return void
     */
    // final private function __clone()
    public function __clone()
    {
        parent::__clone();
    }

    /**
     * Prevent from being unserialized.
     *
     * @return void
     */
    final public function __wakeup()
    {
    }
}
