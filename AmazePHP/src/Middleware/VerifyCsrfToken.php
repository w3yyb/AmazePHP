<?php

namespace AmazePHP\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\TokenMismatchException;
 use AmazePHP\InteractsWithTime;
use Symfony\Component\HttpFoundation\Cookie;
use AmazePHP\SingletonTrait;

class VerifyCsrfToken implements \AmazePHP\MiddlewareInterface
{
    use InteractsWithTime;
    // use SingletonTrait;


 

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The encrypter implementation.
     *
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
     * @return void
     */
    // public function __construct(Encrypter $encrypter)
    public function __construct()
    {
        // $this->app =  app();
        // $this->encrypter =  1;//$encrypter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function process($request, \Closure $next, ...$params)
    {

        if (!config('session.enable')) {
            return $next($request);
        }
        if (
            $this->isReading($request) ||
            // $this->runningUnitTests() ||
            // $this->inExceptArray($request) || /TODO
            $this->tokensMatch($request)
        ) {

            return $next($request);
            // return tap($next($request), function ($response) use ($request) {
            //     if ($this->shouldAddXsrfTokenCookie()) {
            //         $this->addCookieToResponse($request, $response);
            //     }
            // });
        }

        throw new \Exception('CSRF token mismatch.');
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest();


        return is_string(session()->token()) &&
               is_string($token) &&
               hash_equals(session()->token(), $token);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getTokenFromRequest()
    {


        $token = !empty($_POST['_token']) ?$_POST['_token'] :  get_all_headers()['x-csrf-token'] ?? null;

        if (! $token && $header = get_all_headers()['x-xsrf-token'] ?? null) {
            try {
                // $token = CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
                $token = $header;
            } catch (DecryptException $e) {
                $token = '';
            }
        }

        return $token;
    }

    /**
     * Determine if the cookie should be added to the response.
     *
     * @return bool
     */
    public function shouldAddXsrfTokenCookie()
    {
        return $this->addHttpCookie;
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }
        $response =response();
        // var_dump($response);exit;

        //$name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false
        $response->cookie(
            
                'XSRF-TOKEN', $request->session()->token(), 60 * $config['lifetime'],
                $config['path'], $config['domain'], $config['secure'], false, false, $config['same_site'] ?? null
            
        );

        return $response;
    }

    /**
     * Determine if the cookie contents should be serialized.
     *
     * @return bool
     */
    public static function serialized()
    {
        return EncryptCookies::serialized('XSRF-TOKEN');
    }
}
