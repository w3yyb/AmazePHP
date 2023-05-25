<?php

use eftec\bladeone\BladeOne;

function env($key, $default = null)
{
    $apcu_key="env$key";
    if (function_exists('apcu_exists') && apcu_exists($apcu_key)) {
        $apcu_value= apcu_fetch($apcu_key);

        if ($apcu_value === false) {
            return $default;
        }
        switch (strtolower($apcu_value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
            default:
                return $apcu_value;
        }
    }

    $value = getenv($key);
    if(function_exists('apcu_store')){
        apcu_store($apcu_key, $value, 60);
    }

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }

    if (($valueLength = \strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
        return substr($value, 1, -1);
    }

    return $value;
}
function value($value, ...$args)
{
    return $value instanceof Closure ? $value(...$args) : $value;
}


function view($view = null, $data = [])
{
    $views = BASE_PATH  . "/template";
    $cache = BASE_PATH. "/cache";
    $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
    return $blade->run($view, $data);
}



function config($key = null, $default = null)
{

if (!isset($GLOBALS['dotenv'])) {
    (new DotEnv());
}

    $config= LoadConfiguration::getInstance();
    if (is_null($key)) {
        return  $config;
    }

    if (is_array($key)) {
        return $config->set($key);
    }

    return $config->get($key, $default);
}



function cache()
{
    $arguments = func_get_args();

    $cache= Cache::getInstance();


    if (empty($arguments)) {
        return $cache;
    }

    if (is_string($arguments[0])) {
        return $cache->get(...$arguments);
    }

    if (! is_array($arguments[0])) {
        throw new InvalidArgumentException(
            'When setting a value in the cache, you must pass an array of key / value pairs.'
        );
    }

    return $cache->put(key($arguments[0]), reset($arguments[0]), $arguments[1] ?? null);
}


function json2array($json)
{
    return json_decode($json, true);
}

function array2json($array)
{
    return json_encode($array, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}

/*
Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);

 */
function logger($msg, $type='error')
{
    $log=new Log();
    $log::$type($msg);
}


/**
 * The function executes an HTTP request using the GuzzleHttp library in PHP and returns a response status code, body, and headers.
 * 
 * @param url The URL of the HTTP request you want to make.
 * @param method The HTTP method used for the request. It can be GET、HEAD or DELETE. By default, it is set to GET.
 * @param header An array of optional headers sent with the request.
 * 
 * @return An array containing three elements: "status_code", "body", and "header".  The 'status_code' element contains the HTTP status code of the response, 'body' element contains the response body as a string, and the 'header' element contains an array of response headers.
 */
function httpRequest($url, $method = 'GET', $header = [])
{

        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, $url, [
            'headers' => $header
        ]);

        $content= [];
        $content['status_code']= $response->getStatusCode();
        $content['body']= (string) $response->getBody();
        $content['header']= $response->getHeaders();
        return $content;
}

function httpGet($url,$header = [])
{
  return httpRequest($url,'GET',$header);
}

function httpHead($url,$header = [])
{
  return httpRequest($url,'HEAD',$header);
}

function httpDelete($url,$header = [])
{
  return httpRequest($url,'DELETE',$header);
}


/* This function uses the specified method (POST, PUT, or PATCH) and data to send an HTTP request to the specified URL. The data can be used as JSON
or regular format data sending. The function returns the response status code, body, and headers as an array. Header parameters are optional and can be used to send additional headers with the request. */
function httpSend($url, $data, $isJson = true,$method='POST',$header = [])
{
    $client = new \GuzzleHttp\Client();
    if ($isJson) {
        if (is_array($data)) {
            $response = $client->request($method, $url, [
                    'json' =>  $data,
                    'headers' => $header
                ]);
        } else {
            $response = $client->request($method, $url, [
                'body' => $data,
                'headers' => $header
            ]);
        }
    } else {
        $response = $client->request($method, $url, [
            'body' => $data,
            'headers' => $header
        ]);
    }

    $content= [];
    $content['status_code']= $response->getStatusCode();
    $content['body']= (string) $response->getBody();
    $content['header']= $response->getHeaders();
    return $content;
}


function httpPost($url, $data, $isJson = true,$method='POST',$header = [])
{
  return  httpSend($url, $data, $isJson ,$method,$header);
}

function httpPut($url, $data, $isJson = true,$method='PUT',$header = [])
{
  return  httpSend($url, $data, $isJson ,$method,$header);
}

function httpPatch($url, $data, $isJson = true,$method='PATCH',$header = [])
{
  return  httpSend($url, $data, $isJson ,$method,$header);
}

/**
 * @param null $key
 * @param null $default
 * @return mixed
 */
function session($key = null, $default = null)
{
    $session = getSession();
    if (null === $key) {
        return $session;
    }
    if (\is_array($key)) {
        $session->put($key);
        return null;
    }
    return $session->get($key, $default);
}

    /**
     * Get session.
     *
     * @return bool
     */
     function getSession()
    {
        $session=null;
        if ($session === null) {
            $session_id = sessionId();
            if ($session_id === false) {
                return false;
            }
            $session = Session::getInstance();
        }
        return $session;
    }


     /**
     * Get session id.
     *
     * @return bool|mixed
     */
     function sessionId()
    {
        return  md5(uniqid('', true));
    }

    function db()
    {
        $db=DB::getInstance();
       
        return $db;
    }

    function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return UrlGenerator::getInstance();
        }

        return (UrlGenerator::getInstance())->to($path, $parameters, $secure);
    }

    /**
 * @param $name
 * @param array $parameters
 * @return string
 */
function route($name, $parameters = [])
{
    $Route=Router::getInstance();
    $route = $Route::getByRouteName($name);
    if (!$route) {
        return $name;
    }
    return  (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]".$route->url($name,$parameters);
}

function cookie($name, $value = '', $max_age = 0, $path = '/', $domain = '', $secure = false, $http_only = false, $samesite='None')
{
    if (!headers_sent()) {
        $arr_cookie_options = array (
            'expires' =>$max_age +time(), 
            'path' => $path, 
            'domain' => $domain,
            'secure' => $secure, 
            'httponly' => $http_only,
            'samesite' => $samesite // None || Lax  || Strict
            );
        // setcookie($name, $value, $max_age +time(), $path, $domain, $secure, $http_only);
        setcookie($name, $value, $arr_cookie_options);
    }
}



if (! function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     *
     * @return \Illuminate\Support\HtmlString
     */
    function csrf_field()
    {
        return new HtmlString('<input type="hidden" name="_token" value="'.csrf_token().'">');
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function csrf_token()
    {
        $session =  getSession();

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
    }
}


/**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function get_all_headers()
    {
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', strtolower(str_replace('_', ' ', $key)));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }

/**
 * @param $data
 * @param int $options
 * @return Response
 */
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    header('Content-Type: application/json');
    return json_encode($data, $options);
}

/**
 * @param $xml
 * @return Response
 */
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    header('Content-Type: text/xml');
    return  $xml;
}

function jsonp($data, $callback_name = 'callback')
{
    if (!\is_scalar($data) && null !== $data) {
        $data = json_encode($data);
    }
    header('Content-Type: application/javascript');
    return "$callback_name($data)";
}


function redirect($location, $status = 302, $headers = [])
{
    header('Location: ' . $location, true, $status);
}
