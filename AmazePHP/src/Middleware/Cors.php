<?php
 
declare (strict_types = 1);

namespace AmazePHP\Middleware;

use Closure;

/**
 * 跨域请求支持
 */
class Cors implements \AmazePHP\MiddlewareInterface {
    protected $cookieDomain;

    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, Origin, Accept, Content-Length',
    ];

    public function __construct()
    {
        $this->cookieDomain = config('cors.paths');
        // var_dump($this->cookieDomain);
    }

    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array   $header
     * @return Response
     */
    public function            process($request, \Closure $next,...$header)       //         handle($request, Closure $next, ? array $header = [])
    {


        if ($_SERVER['REQUEST_METHOD']=== 'OPTIONS') {
            header('Allow: GET,HEAD');//TODO
        }


        // Check if we're dealing with CORS and if we should handle it
        if (! $this->shouldRun($request)) {
            return $next($request);
        }

        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;

        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');

           // if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {  //TODO
            //    $header['Access-Control-Allow-Origin'] = $origin;
         //   } else {
                $header['Access-Control-Allow-Origin'] = '*';
        //    }
        }
        // $header['Access-Control-Allow-Origin'] = '*';

        // var_dump($header);exit;
        foreach ($header as $key => $value) {

            if (!is_string($key) || !is_string($value)) {
                continue;
            }
            $key=  (string)$key;
            $value= (string)$value;
            // var_dump($key,$value,'@@@@@@@@');
            header($key . ':' . $value);
        }
        // return $next($request)->withHeaders($header);
        return $next($request);
    }


    /**
     * Determine if the request has a URI that should pass through the CORS flow.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldRun( $request): bool
    {
        return $this->isMatchingPath($request);
    }

    /**
     * The the path from the config, to see if the CORS Service should run
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isMatchingPath( $request): bool
    {
        // Get the paths from the config or the middleware
        $paths = config('cors.paths');// $this->container['config']->get('cors.paths', []);

        foreach ($paths as $path) {
            if ($path !== '/') {
                $path = trim($path, '/');
            }

            if ($request->fullUrlIs($path) || $request->is($path)) {
                return true;
            }
        }

        return false;
    }
}
