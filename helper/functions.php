<?php

use eftec\bladeone\BladeOne;

function env($key, $default = null)
{
    $apcu_key="env$key";
    if (apcu_exists($apcu_key)) {
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
    apcu_store($apcu_key, $value, 60);

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
    $config= LoadConfiguration::getInstance();
    // var_dump($config);

    // global $config;
    // debug_print_backtrace();
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
function logs($msg, $type='error')
{
    $log=new Log();
    $log::$type($msg);
}


/**
 * 该函数使用 PHP 中的 GuzzleHttp 库执行 HTTP 请求，并返回响应状态代码、正文和标头。
 * 
 * @param url 您要发出的 HTTP 请求的 URL。
 * @param method 用于请求的 HTTP 方法。它可以是 GET、HEAD 或 DELETE。默认情况下，它设置为 GET。
 * @param header 与请求一起发送的可选标头数组。
 * 
 * @return 包含三个元素的数组：“status_code”、“body”和“header”。 'status_code' 元素包含响应的 HTTP 状态代码，'body'
 * 元素包含作为字符串的响应正文，'header' 元素包含响应标头的数组。
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


/* 此函数使用指定的方法（POST、PUT 或 PATCH）和数据向指定的 URL 发送 HTTP 请求。数据可以作为 JSON
或常规格式数据发送。该函数以数组形式返回响应状态代码、正文和标头。标头参数是可选的，可用于随请求发送其他标头。 */
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
