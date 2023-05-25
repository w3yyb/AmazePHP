<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string = 'http:///posts/33';
        $id=33;

        $email =   url("/posts/{$id}"); // http://example.com/posts/1


        $this->assertSame($string, $email);
    }

    public function testSet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
       
        config(['route' => [[
            ['GET'],
            '/hello/{id}/foo/{sid}',
            ['aaaaaaa', 'bar'],
            'nameroute1'//Named Route
    ]]]);



    $router = Router::getInstance();
foreach (config('route') as $key => $value) {
    $router->addRoute($value[0][0], $value[1], $value[2], $value[3] ?? null);
}



        $string = 'http:///hello/1/foo/2';

        $email = route('nameroute1', ['id' => 1, 'sid' => 2]);

        $this->assertSame($string, $email);


    }
}