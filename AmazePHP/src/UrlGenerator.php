<?php
namespace AmazePHP;

use Illuminate\Container\Container;

class UrlGenerator
{
    // use SingletonTrait;

    // private $cache;

    public function __construct()
    {

 
 
    }

  
    /**
     * Get the full URL for the current request.
     *
     * @return string
     */
    public function full()
    {
        return $this->fullUrl();
    }

    public function fullUrl()
    {

        $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $actual_link;
 
    }


    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public function current()
    {
        $url = strtok($this->full(), '?');

        return   $url;
    }

    /**
     * Get the URL for the previous request.
     *
     * @param  mixed  $fallback
     * @return string
     */
    public function previous($fallback = false)
    {
        $referrer =      $_SERVER['HTTP_REFERER']?? null;       //$this->request->headers->get('referer');

        $url = $referrer ? $this->to($referrer) : $this->getPreviousUrlFromSession();

        if ($url) {
            return $url;
        } elseif ($fallback) {
            return $this->to($fallback);
        }

        return $this->to('/');
    }

    protected function getPreviousUrlFromSession()
    {
        Container::getInstance()->singleton('Session', 'Session');

        $session = Container::getInstance()->make(Session::class);

        return $session ? $session->previousUrl() : null;
    }



    public function to($path, $extra = [], $secure = null)
    {
        // First we will check if the URL is already a valid URL. If it is we will not
        // try to generate a new one but will simply return the URL as is, which is
        // convenient since developers do not always have to check if it's valid.
        if ($this->isValidUrl($path)) {
            return $path;
        }

        // $tail = implode('/', array_map(
        //     'rawurlencode', (array) $this->formatParameters($extra))
        // );
        $tail='';

        // Once we have the scheme we will compile the "tail" by collapsing the values
        // into a single string delimited by slashes. This just makes it convenient
        // for passing the array of parameters to this URL as a list of segments.
        $root =    (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";//      $this->formatRoot($this->formatScheme($secure));


        [$path, $query] = $this->extractQueryString($path);

        return $this->format(
            $root, '/'.trim($path.'/'.$tail, '/')
        ).$query;
    }


    protected function extractQueryString($path)
    {
        if (($queryPosition = strpos($path, '?')) !== false) {
            return [
                substr($path, 0, $queryPosition),
                substr($path, $queryPosition),
            ];
        }

        return [$path, ''];
    }

    public function format($root, $path, $route = null)
    {
        $path = '/'.trim($path, '/');

        return trim($root.$path, '/');
    }


    public function isValidUrl($path)
    {
        if (! preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }
   
}
