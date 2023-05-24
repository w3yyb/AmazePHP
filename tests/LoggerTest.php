<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }

        logger('thisistherestmsg');//error log

        $string = 'thisistherestmsg';
        $email = file_get_contents(BASE_PATH . '/cache/log-'.date('Y-m-d').'.log');

        $match =preg_match('/'.$string.'/', $email);

        
        $this->assertEquals(1, $match);
    }



    public function testGetWarning(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }

        logger('thisistherestmsgwarn','warning');

        $string = 'WARNING: thisistherestmsgwarn';
        $email = file_get_contents(BASE_PATH . '/cache/log-'.date('Y-m-d').'.log');

        $match =preg_match('/'.$string.'/', $email);

        
        $this->assertEquals(1, $match);
    }

     
}