<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }

        // config(['route' => [
        //     [
        //         ['PUT,GET'],
        //         '/hellotest',
        //         function () {
        //             echo 'Hello AmazePHP!';
        //         },
        //     ],
        // ]]);

        $string = 'Hello AmazePHP!';


        

        $response  = file_get_contents('http://tool2.p2hp.com/hello');

        $this->assertSame($string, $response);
    }

    
}