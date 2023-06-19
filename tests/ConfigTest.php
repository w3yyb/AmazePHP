<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;

final class ConfigTest extends TestCase
{
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string = 'en';

        $email = config('app.locale');

        $this->assertSame($string, $email);
    }

    public function testSet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }

$container =   Container::getInstance();
$container->singleton('AmazePHP\LoadConfiguration', 'AmazePHP\LoadConfiguration');

       
        config(['app.timezone' => 'America/Chicago']);

        $string = 'America/Chicago';

        $email = config('app.timezone');

        $this->assertSame($string, $email);


    }
}