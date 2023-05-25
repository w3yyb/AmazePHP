<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    

    public function testSet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
       
        session(["name" => "value"]);

        $string = 'value';

        $email = session('name');

        $this->assertSame($string, $email);


    }
}