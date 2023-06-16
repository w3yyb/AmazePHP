<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class DbTest extends TestCase
{
    public function testSelect(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        include_once 'AmazePHP/src/DotEnv.php';
        (new \AmazePHP\DotEnv());
        $string = 'test1';

        $email = $results = db()->select("*")->from('users')->where("name like '%test%'")->toList()[0]['name'];


        $this->assertSame($string, $email);
    }

    public function testInsert(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        include_once 'AmazePHP/src/DotEnv.php';
        (new \AmazePHP\DotEnv());
       
        db()->insert("users"
    ,['name','email','password']
    ,['kevinkal','email@email.com'.rand(1,10000),'123456']);

        $string = 'kevinkal';

        $email = $results = db()->select("*")->from('users')->where("name like '%kevinkal%'")->toList()[0]['name'];

        $this->assertSame($string, $email);


    }
}