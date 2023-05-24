<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class HttpClientTest extends TestCase
{
    public function testGet(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string = 200;

       $response= httpGet('http://httpbin.org/get')['status_code'];

        $this->assertSame($string, $response);
    }


    public function testDelete(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string =  'headervalue';

       $response= httpDelete('http://httpbin.org/delete',['headername'=>'headervalue']);
       $response = json_decode($response['body'],true)['headers']['Headername'];

        $this->assertSame($string, $response);
    }


    public function testPost(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string =  'senddatavalue';

       $response= httpPost('http://httpbin.org/post',['senddataname'=>'senddatavalue']);
       $response = json_decode($response['body'],true)['data'];

       $response = json_decode($response,true)['senddataname'];



        $this->assertSame($string, $response);
    }





    public function testPut(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string =  'senddatavalue';

       $response= httpPut('http://httpbin.org/put',['senddataname'=>'senddatavalue']);
       $response = json_decode($response['body'],true)['data'];

       $response = json_decode($response,true)['senddataname'];



        $this->assertSame($string, $response);
    }





    public function testPatch(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . '/..');

        }
        $string =  'senddatavalue';

       $response= httpPatch('http://httpbin.org/patch',['senddataname'=>'senddatavalue']);
       $response = json_decode($response['body'],true)['data'];

       $response = json_decode($response,true)['senddataname'];



        $this->assertSame($string, $response);
    }
    
}