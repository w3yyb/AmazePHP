<?php


use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken  
{
    use InteractsWithTime;
    use SingletonTrait;


 
 
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

 
    // public function __construct(Encrypter $encrypter)
    public function __construct()
    {
        $this->encrypter =  1;//$encrypter;

        if (
            $this->isReading() ||
            //$this->inExceptArray($request) || //TODO
            $this->tokensMatch()
        ) {
            // success
        }else{
            throw new Exception('CSRF token mismatch.');
        }
    }

  
    

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading()
    {
        return in_array($_SERVER['REQUEST_METHOD'], ['HEAD', 'GET', 'OPTIONS']);
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
    protected function tokensMatch()
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
     * Determine if the cookie contents should be serialized.
     *
     * @return bool
     */
    public static function serialized()
    {
        return EncryptCookies::serialized('XSRF-TOKEN');
    }
}
