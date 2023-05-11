<?php

class Router
{
    private $routes = [];
    private $routeCount = 0;

    public function addRoute($method, $url, $callback)
    {
        if ($url !== '/') {//去除url尾部斜杠
            while ($url !== $url = rtrim($url, '/'));//不应该去除url尾部斜杠，以后要改
        }

        $this->routes[] = ['method' => $method, 'url' => $url, 'callback' => $callback];
        $this->routeCount++;
    }

    public function doRouting()
    {
        $is_match=0;
        // I used PATH_INFO instead of REQUEST_URI, because the
        // application may not be in the root direcory
        // and we dont want stuff like ?var=value
        $reqUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);//$_SERVER['PATH_INFO'];

       

        $reqMet = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            // convert urls like '/users/:uid/posts/:pid' to regular expression

            $patterns = array('/\[:[a-zA-Z0-9\_\-]+\]/','/:[a-zA-Z0-9\_\-]+/');
            $replace = array('([a-zA-Z0-9\-\_]*)','([a-zA-Z0-9\-\_]+)');


            // $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['url'])) . "$@D";
            $pattern = "@^" . preg_replace($patterns, $replace, $route['url']) . "$@D";

            $matches = array();
            // check if the current request matches the expression
            if (preg_match($pattern, $reqUrl, $matches)) {
                // remove the first match
                array_shift($matches);

                foreach ($matches as $key => $value) {
                    if (empty($value)) {
                        unset($matches[$key]);
                    }
                }
                // call the callback with the matched positions as params

                if ($route['method'] !=='*'  && $reqMet !=='HEAD'  && (
                    !empty($route['method']) &&
                    !in_array($reqMet, explode(',', $route['method'])))
                ) {
                    throw new Exception("405 Not Allowed");
                }
                

                return call_user_func_array($route['callback'], $matches);
            } else {
                $is_match++;
            }
        }

        if ($is_match == $this->routeCount) {
            throw new Exception("404 Not Found");
        }
    }
}


//autoload
// spl_autoload_register(function ($class_name) {
//     require_once __DIR__ . '/' .  str_replace('\\', '/', $class_name) . '.php';
// });
