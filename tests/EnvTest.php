<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class EnvTest extends TestCase
{
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }

        include_once 'lib/DotEnv.php';
        (new DotEnv());


        $string = true;

        $email = env('APP_DEBUG');

        $this->assertSame($string, $email);
    }

    public function testGetDefault(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        include_once 'lib/DotEnv.php';
        (new DotEnv());
        config(['app.timezone' => 'America/Chicago']);

        $string = 'default';


        $email = env('APP_NOTHING', 'default');


        $this->assertSame($string, $email);


    }
}