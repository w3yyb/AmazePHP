<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    

    public function testSet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
       
        cache(['key' => 'value'], 10);// Expires after 10 seconds

        $string = 'value';

        $email = cache('key');

        $this->assertSame($string, $email);


    }


    public function testSetExpire(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
       
        cache(['key2' => 'value2'], 10);// Expires after 10 seconds

        $string = 'value2';
        sleep(11);

        $email = cache('key2');

        $this->assertFalse($email);


    }
}