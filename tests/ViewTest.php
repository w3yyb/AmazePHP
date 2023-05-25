<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use eftec\bladeone\BladeOne;

final class ViewTest extends TestCase
{
    
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }


        $views = dirname(__DIR__). "/template";
        $cache = dirname(__DIR__). "/cache";
        $blade = new  BladeOne($views, $cache, BladeOne::MODE_AUTO);

$setvars='hello world~';


     $string=   $blade->run("test", [
            "setvars" => $setvars,
           
        ]);


 

        $this->assertSame($setvars, $string);
    }
     

 
}