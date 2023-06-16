<?php
namespace AmazePHP;


use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler as StreamHandler;
class Log
{
    protected  $_manager = null;
    protected static $_instance = [];

     
    public  function __construct()
    {

        $configs = config('log', []);
        foreach ($configs as $channel => $config) {
            
            $logger = static::$_instance[$channel] = new Logger($channel);
            foreach ($config['handlers'] as $handler_config) {
                $handler = new $handler_config['class'](... \array_values($handler_config['constructor']));
                if (isset($handler_config['formatter'])) {
                    $formatter = new $handler_config['formatter']['class'](... \array_values($handler_config['formatter']['constructor']));
                    $handler->setFormatter($formatter);
                }
                $logger->pushHandler($handler);
            }
        }
    }

  

    /**
     * @param string $name
     * @return Logger;
     */
    public static function channel($name = 'default')
    {
        return static::$_instance[$name] ?? null;
    }

    


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public  static function __callStatic($name, $arguments)
    {

        return static::channel('default')->{$name}(... $arguments);
    }
  
 
    
}
