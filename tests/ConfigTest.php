<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

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
       
        config(['app.timezone' => 'America/Chicago']);

        $string = 'America/Chicago';

        $email = config('app.timezone');

        $this->assertSame($string, $email);


    }
}