<?php
namespace AmazePHP;


// use WondPHP\Http\UploadFile;
// use Symfony\Component\HttpFoundation\AcceptHeader;
// use Illuminate\Support\Str;
// use WondPHP\Http\Protocols\Session;

/**
 * Class Request
 *
 */
class Request  
{
    use SingletonTrait;
    
     
    public const HEADER_FORWARDED = 0b000001; // When using RFC 7239
    public const HEADER_X_FORWARDED_FOR = 0b000010;
    public const HEADER_X_FORWARDED_HOST = 0b000100;
    public const HEADER_X_FORWARDED_PROTO = 0b001000;
    public const HEADER_X_FORWARDED_PORT = 0b010000;
    public const HEADER_X_FORWARDED_PREFIX = 0b100000;

    /** @deprecated since Symfony 5.2, use either "HEADER_X_FORWARDED_FOR | HEADER_X_FORWARDED_HOST | HEADER_X_FORWARDED_PORT | HEADER_X_FORWARDED_PROTO" or "HEADER_X_FORWARDED_AWS_ELB" or "HEADER_X_FORWARDED_TRAEFIK" constants instead. */
    public const HEADER_X_FORWARDED_ALL = 0b1011110; // All "X-Forwarded-*" headers sent by "usual" reverse proxy
    public const HEADER_X_FORWARDED_AWS_ELB = 0b0011010; // AWS ELB doesn't send X-Forwarded-Host
    public const HEADER_X_FORWARDED_TRAEFIK = 0b0111110; // All "X-Forwarded-*" headers sent by Traefik reverse proxy

    public const METHOD_HEAD = 'HEAD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PURGE = 'PURGE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_TRACE = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';
    private static $trustedHeaderSet = -1;

   

    /**
     * @var string
     */
    public $controller = null;

    /**
     * @var string
     */
    public $action = null;


    protected static $trustedProxies = [];
    /**
     * Session instance.
     *
     * @var Session
     */
    public $session = null;

    /**
     * Properties.
     *
     * @var array
     */
    public $properties = array();

    /**
     * Http buffer.
     *
     * @var string
     */
    protected $_buffer = null;

    /**
     * Request data.
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Header cache.
     *
     * @var array
     */
    protected static $_headerCache = array();

    /**
     * Get cache.
     *
     * @var array
     */
    protected static $_getCache = array();

    /**
     * Post cache.
     *
     * @var array
     */
    protected static $_postCache = array();

    /**
     * Enable cache.
     *
     * @var bool
     */
    protected static $_enableCache = true;



    /**
     * Request constructor.
     *
     * @param $buffer
     */
    public function __construct($buffer='')
    {

        $buffer = '';
        // (1) 请求行
        $buffer .= $_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'].' '.$_SERVER['SERVER_PROTOCOL']."\r\n";
        // (2) 请求Headers
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                $key = str_replace('_', '-', $key);
                $buffer .= $key.': '.$value."\r\n";
            }
        }
        // (3) 空行
        $buffer .= "\r\n";
        // (4) 请求Body  如果请求header里Contente-Type是 multipart/form-data,或application/x-www-form-urlencoded或application/octet-stream则需要用 $_POST($_GET)或$_FILES来接收.
        $buffer .= file_get_contents('php://input');
        // var_dump($raw);exit;


        $this->_buffer = $buffer;
    }


    /**
     * @return mixed|null
     */
    public function all()
    {
        return $this->post() + $this->get();
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function input($name, $default = null)
    {
        $post = $this->post();
        if (isset($post[$name])) {
            return $post[$name];
        }
        $get = $this->get();
        return isset($get[$name]) ? $get[$name] : $default;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function only(array $keys)
    {
        $all = $this->all();
        $result = [];
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        return $result;
    }

    /**
     * @param array $keys
     * @return mixed|null
     */
    public function except(array $keys)
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    /**
     * @param null $name
     * @return null| array | UploadFile
     */
    public function file($name = null,$type="image")
    {
        $files = $_FILES;//;parent::file($name);
        if (null === $files) {
            return $name === null ? [] : null;
        }
        if ($name !== null) {
            $files=array_values($files)[0] ?? '';
            if (!empty($files)) {
                return new UploadFile($files['tmp_name'], $files['name'], $files['type'], $files['error'], $files['size'],$type);
            } else {
                return '';
            }
        }
        $upload_files = [];
        foreach ($files as $name => $file) {
            $upload_files[$name] = new UploadFile($file['tmp_name'], $file['name'], $file['type'], $file['error'],$file['size'],$type);
        }
        return $upload_files;
    }

    /**
     * @return string
     */
    public function getRemoteIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return $_SERVER['REMOTE_PORT']	;
    }

    /**
     * @return string
     */
    public function getLocalIp()
    {
        return gethostbyname($_SERVER["SERVER_NAME"]);
    }

    /**
     * @return int
     */
    public function getLocalPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * @param bool $safe_mode
     * @return string
     */
    public function getRealIp($safe_mode = true)
    {
        $remote_ip = $this->getRemoteIp();
        if ($safe_mode && !static::isIntranetIp($remote_ip)) {
            return $remote_ip;
        }
        return $this->header('client-ip', $this->header(
            'x-forwarded-for',
            $this->header('x-real-ip', $this->header(
                'x-client-ip',
                $this->header('via', $remote_ip)
            ))
        ));
    }

    /**
     * @return string
     */
    public function url()
    {
        return    (empty($_SERVER['HTTPS']) ? 'http' : 'https') .'://' . $this->host() . $this->path();
    }

    /**
     * @return string
     */
    public function fullUrl()
    {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $this->host() . $this->uri();
    }
     /**
     * Determine if the current request URL and query string matches a pattern.
     *
     * @param  mixed  ...$patterns
     * @return bool
     */
    public function fullUrlIs(...$patterns)
    {
        $url = $this->fullUrl();

        foreach ($patterns as $pattern) {

            if (Str::is($pattern, $url)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param  mixed  ...$patterns
     * @return bool
     */
    public function is(...$patterns)
    {
        $path = $this->decodedPath();
        foreach ($patterns as $pattern) {
        $path=    ltrim ($path,'/');
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the current decoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }


    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

     /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }

     /**
     * Returns true if the request is an XMLHttpRequest.
     *
     * It works if your JavaScript library sets an X-Requested-With HTTP header.
     * It is known to work with common JavaScript frameworks:
     *
     * @see https://wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return bool true if the request is an XMLHttpRequest, false otherwise
     */
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->header('X-Requested-With');
    }

    /**
     * Determine if the request is the result of a prefetch call.
     *
     * @return bool
     */
    public function prefetch()
    {
        return strcasecmp($_SERVER['HTTP_X_MOZ'] ?? '', 'prefetch') === 0 ||
               strcasecmp($this->header('Purpose') ?? '', 'prefetch') === 0;
    }

    /**
     * @return bool
     */
    public function isPjax()
    {
        return (bool)$this->header('X-PJAX');
    }

    /**
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->isAjax() && ! $this->isPjax() && $this->acceptsAnyContentType()) || $this->wantsJson(); 

    }


    /**
     * Determine if the current request accepts any content type.
     *
     * @return bool
     */
    public function acceptsAnyContentType()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return \count($acceptable) === 0 || (
            isset($acceptable[0]) && ($acceptable[0] === '*/*' || $acceptable[0] === '*')
        );
    }

    public function getAcceptableContentTypes()
    {
        $accept =$this->header('accept') ?? [];
        $accepts =  array_keys(AcceptHeader::fromString($accept)->all());
        return $accepts;
    }

    /**
     * Determine if the current request is asking for JSON.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) &&  (strstr($acceptable[0], '/json') ||  strstr($acceptable[0], '+json'));
    }

    public function isJson()
    {
        return Str::contains($this->header('content-type') ?? '', ['/json', '+json']);
    }

    // public function isJson2()
    // {
    //     return $this->hasHeader('Content-Type') &&
    //            Str::contains($this->header('Content-Type')[0], 'json');
    // }
     


    /**
     * @return bool
     */
    public function acceptJson()
    {
        return $this->accepts('application/json');

    }
// todo
    public function controller()
    {
        return $this->controller;//$GLOBALS['classname'];
    }
//todo 
    public function action()
    {
        return $this->action;
        // return $GLOBALS['actionname'];
    }
    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array  $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        if (\count($accepts) === 0) {
            return true;
        }

        $types = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/').'/*') {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Determine if the given content types match.
     *
     * @param  string  $actual
     * @param  string  $type
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }

        $split = explode('/', $actual);

        return isset($split[1]) && preg_match('#'.preg_quote($split[0], '#').'/.+\+'.preg_quote($split[1], '#').'#', $type);
    }

    /**
     * @param string $ip
     * @return bool
     */
    public static function isIntranetIp($ip)
    {
        $reserved_ips = [
            '167772160'  => 184549375,  /*    10.0.0.0 -  10.255.255.255 */
            '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
            '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
            '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
        ];

        $ip_long = ip2long($ip);

        foreach ($reserved_ips as $ip_start => $ip_end) {
            if (($ip_long >= $ip_start) && ($ip_long <= $ip_end)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Retrieve an old input item.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function old($key = null, $default = null)
    {
        return session()->getOldInput($key, $default) ?? $default;
        // return $this->hasSession() ? session()->getOldInput($key, $default) : $default;
    }

    /**
     * Flash the input for the current request to the session.
     *
     * @return void
     */
    public function flash()
    {
         session()->flashInput($this->all());
    }

    /**
     * Flash only some of the input to the session.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function flashOnly($keys)
    {
        session()->flashInput(
            $this->only(is_array($keys) ? $keys : func_get_args())
        );
    }

    /**
     * Flash only some of the input to the session.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function flashExcept($keys)
    {
        session()->flashInput(
            $this->except(is_array($keys) ? $keys : func_get_args())
        );
    }

    /**
     * Flush all of the old input from the session.
     *
     * @return void
     */
    public function flush()
    {
        session()->flashInput([]);
    }

    public function hasSession()
    {
        return null !== $this->session;
    }

    public function isMethodCacheable()
    {
        return \in_array($this->getMethod(), ['GET', 'HEAD']);
    }

    /**
     * Gets the request "intended" method.
     *
     * If the X-HTTP-Method-Override header is set, and if the method is a POST,
     * then it is used to determine the "real" intended HTTP method.
     *
     * The _method request parameter can also be used to determine the HTTP method,
     * but only if enableHttpMethodParameterOverride() has been called.
     *
     * The method is always an uppercased string.
     *
     * @return string The request method
     *
     * @see getRealMethod()
     */
    public function getMethod()
    {
        if (null !== $this->method) {
            return $this->method;
        }

        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ??  'GET');

        if ('POST' !== $this->method) {
            return $this->method;
        }

        $method = $this->headers->get('X-HTTP-METHOD-OVERRIDE');

        if (!$method && self::$httpMethodParameterOverride) {
            $method = $this->request->get('_method', $this->query->get('_method', 'POST'));
        }

        if (!\is_string($method)) {
            return $this->method;
        }

        $method = strtoupper($method);

        if (\in_array($method, ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'PATCH', 'PURGE', 'TRACE'], true)) {
            return $this->method = $method;
        }

        if (!preg_match('/^[A-Z]++$/D', $method)) {
            throw new SuspiciousOperationException(sprintf('Invalid method override "%s".', $method));
        }

        return $this->method = $method;
    }


    /**
     * Gets the Etags.
     *
     * @return array The entity tags
     */
    public function getETags()
    {
        return preg_split('/\s*,\s*/', $this->header('if_none_match', ''), -1, \PREG_SPLIT_NO_EMPTY);
    }

      /**
     * Sets a list of trusted proxies.
     *
     * You should only list the reverse proxies that you manage directly.
     *
     * @param array $proxies          A list of trusted proxies, the string 'REMOTE_ADDR' will be replaced with $_SERVER['REMOTE_ADDR']
     * @param int   $trustedHeaderSet A bit field of Request::HEADER_*, to set which headers to trust from your proxies
     */
    public static function setTrustedProxies(array $proxies, int $trustedHeaderSet)
    {
        if (self::HEADER_X_FORWARDED_ALL === $trustedHeaderSet) {
            trigger_deprecation('symfony/http-foundation', '5.2', 'The "HEADER_X_FORWARDED_ALL" constant is deprecated, use either "HEADER_X_FORWARDED_FOR | HEADER_X_FORWARDED_HOST | HEADER_X_FORWARDED_PORT | HEADER_X_FORWARDED_PROTO" or "HEADER_X_FORWARDED_AWS_ELB" or "HEADER_X_FORWARDED_TRAEFIK" constants instead.');
        }
        self::$trustedProxies = array_reduce($proxies, function ($proxies, $proxy) {
            if ('REMOTE_ADDR' !== $proxy) {
                $proxies[] = $proxy;
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $proxies[] = $_SERVER['REMOTE_ADDR'];
            }

            return $proxies;
        }, []);
        self::$trustedHeaderSet = $trustedHeaderSet;
    }






    //modify request content
    public function modify($name=null, $value=null,$type='get')
    {
        $this->_data[$type][$name] =$value;
    }

    /**
     * $_GET.
     *
     * @param null $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name = null, $default = null)
    {
        if (!isset($this->_data['get'])) {
            $this->parseGet();
        }
        if (null === $name) {
            return $this->_data['get'];
        }
        return isset($this->_data['get'][$name]) ? $this->_data['get'][$name] : $default;
    }

    /**
     * $_POST.
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function post($name = null, $default = null)
    {
        if (!isset($this->_data['post'])) {
            $this->parsePost();
            if(empty($this->_data['post']) && $_POST){
                $this->_data['post'] = $_POST;
            }
        }
        if (null === $name) {
            return $this->_data['post'];
        }
        return isset($this->_data['post'][$name]) ? $this->_data['post'][$name] : $default;
    }

    /**
     * Get header item by name.
     *
     * @param null $name
     * @param null $default
     * @return string|null
     */
    public function header($name = null, $default = null)
    {
        if (!isset($this->_data['headers'])) {
            $this->parseHeaders();
        }
        if (null === $name) {
            return $this->_data['headers'];
        }
        $name = \strtolower($name);
        return isset($this->_data['headers'][$name]) ? $this->_data['headers'][$name] : $default;
    }

    /**
     * Get cookie item by name.
     *
     * @param null $name
     * @param null $default
     * @return string|null
     */
    public function cookie($name = null, $default = null)
    {
        if (!isset($this->_data['cookie'])) {
            \parse_str(\str_replace('; ', '&', $this->header('cookie')), $this->_data['cookie']);
        }
        if ($name === null) {
            return $this->_data['cookie'];
        }
        return isset($this->_data['cookie'][$name]) ? $this->_data['cookie'][$name] : $default;
    }

    /**
     * Get upload files.
     *
     * @param null $name
     * @return array|null
     */
    // public function file_2($name = null)
    // {
    //     if (!isset($this->_data['files'])) {
    //         $this->parsePost();
    //     }
    //     if (null === $name) {
    //         return $this->_data['files'];
    //     }
    //     return isset($this->_data['files'][$name]) ? $this->_data['files'][$name] : null;
    // }

    /**
     * Get method.
     *
     * @return string
     */
    public function method()
    {
        if (!isset($this->_data['method'])) {
            $this->parseHeadFirstLine();
        }
        return $this->_data['method'];
    }

    /**
     * Get http protocol version.
     *
     * @return string.
     */
    public function protocolVersion()
    {
        if (!isset($this->_data['protocolVersion'])) {
            $this->parseProtocolVersion();
        }
        return $this->_data['protocolVersion'];
    }

    /**
     * Get host.
     *
     * @param bool $without_port
     * @return string
     */
    public function host($without_port = false)
    {
        $host = $this->header('host');
        if ($without_port && $pos = \strpos($host, ':')) {
            return \substr($host, 0, $pos);
        }
        return $host;
    }

    /**
     * Get uri.
     *
     * @return mixed
     */
    public function uri()
    {
        if (!isset($this->_data['uri'])) {
            $this->parseHeadFirstLine();
        }
        return $this->_data['uri'];
    }

    /**
     * Get path.
     *
     * @return mixed
     */
    public function path()
    {
        if (!isset($this->_data['path'])) {
            $this->_data['path'] = \parse_url($this->uri(), PHP_URL_PATH);
        }
        return $this->_data['path'];
    }

    /**
     * Get query string.
     *
     * @return mixed
     */
    public function queryString()
    {
        if (!isset($this->_data['query_string'])) {
            $this->_data['query_string'] = \parse_url($this->uri(), PHP_URL_QUERY);
        }
        return $this->_data['query_string'];
    }

    /**
     * Get session.
     *
     * @return bool
     */
    public function session()
    {
        if ($this->session === null) {
            $session_id = session()->sessionId();
            if ($session_id === false) {
                return false;
            }
            $this->session =session();// new Session($session_id);
        }
        return $this->session;
    }

    /**
     * Get session id.
     *
     * @return bool|mixed
     */
    public function sessionId()
    {
        return session()->sessionId();
        // if (!isset($this->_data['sid'])) {
        //     $session_name = Http::sessionName();
        //     $sid = $this->cookie($session_name);
        //     if ($sid === '' || $sid === null) {
        //         if (0) {
        //             echo('Request->session() fail, header already send');
        //             return false;
        //         }
        //         $sid = static::createSessionId();
        //         $cookie_params = \session_get_cookie_params();
        //         $lifetime =config('session.lifetime') ??$cookie_params['lifetime'] ;

        //         $header= array($session_name . '=' . $sid
        //             . (empty($cookie_params['domain']) ? '' : '; Domain=' . $cookie_params['domain'])
        //             . (empty($lifetime) ? '' : '; Max-Age=' . ($lifetime *60))
        //             . (empty($cookie_params['path']) ? '' : '; Path=' . $cookie_params['path'])
        //             . (empty($cookie_params['samesite']) ? '' : '; SameSite=' . $cookie_params['samesite'])
        //             . (!$cookie_params['secure'] ? '' : '; Secure')
        //             . (!$cookie_params['httponly'] ? '' : '; HttpOnly'));
        //         header("Set-Cookie: $header[0]");
        //     }
        //     $this->_data['sid'] = $sid;
        // }
        return $this->_data['sid'];
    }

    /**
     * Get http raw head.
     *
     * @return string
     */
    public function rawHead()
    {
        if (!isset($this->_data['head'])) {
            $this->_data['head'] = \strstr($this->_buffer, "\r\n\r\n", true);
        }
        return $this->_data['head'];
    }

    /**
     * Get http raw body.
     *
     * @return string
     */
    public function rawBody()
    {
        return \substr($this->_buffer, \strpos($this->_buffer, "\r\n\r\n") + 4);
    }

    /**
     * Get raw buffer.
     *
     * @return string
     */
    public function rawBuffer()
    {
        return $this->_buffer;
    }

    /**
     * Enable or disable cache.
     *
     * @param $value
     */
    public static function enableCache($value)
    {
        static::$_enableCache = (bool)$value;
    }

    /**
     * Parse first line of http header buffer.
     *
     * @return void
     */
    protected function parseHeadFirstLine()
    {
        $first_line = \strstr($this->_buffer, "\r\n", true);
        $tmp = \explode(' ', $first_line, 3);
        $this->_data['method'] = $tmp[0];
        $this->_data['uri'] = isset($tmp[1]) ? $tmp[1] : '/';
    }

    /**
     * Parse protocol version.
     *
     * @return void
     */
    protected function parseProtocolVersion()
    {
        $first_line = \strstr($this->_buffer, "\r\n", true);
        $protoco_version = substr(\strstr($first_line, 'HTTP/'), 5);
        $this->_data['protocolVersion'] = $protoco_version ? $protoco_version : '1.0';
    }

    /**
     * Parse headers.
     *
     * @return void
     */
    protected function parseHeaders()
    {
        $this->_data['headers'] = array();
        $raw_head = $this->rawHead();
        $head_buffer = \substr($raw_head, \strpos($raw_head, "\r\n") + 2);
        $cacheable = static::$_enableCache && !isset($head_buffer[2048]);
        if ($cacheable && isset(static::$_headerCache[$head_buffer])) {
            $this->_data['headers'] = static::$_headerCache[$head_buffer];
            return;
        }
        $head_data = \explode("\r\n", $head_buffer);
        foreach ($head_data as $content) {
            if (false !== \strpos($content, ':')) {
                list($key, $value) = \explode(':', $content, 2);
                $this->_data['headers'][\strtolower($key)] = \ltrim($value);
            } else {
                $this->_data['headers'][\strtolower($content)] = '';
            }
        }
        if ($cacheable) {
            static::$_headerCache[$head_buffer] = $this->_data['headers'];
            if (\count(static::$_headerCache) > 128) {
                unset(static::$_headerCache[key(static::$_headerCache)]);
            }
        }
    }

    /**
     * Parse head.
     *
     * @return void
     */
    protected function parseGet()
    {
        $query_string = $this->queryString();
        $this->_data['get'] = array();
        if ($query_string === '') {
            return;
        }
        $cacheable = static::$_enableCache && !isset($query_string[1024]);
        if ($cacheable && isset(static::$_getCache[$query_string])) {
            $this->_data['get'] = static::$_getCache[$query_string];
            return;
        }
        \parse_str($query_string ?? '', $this->_data['get']);

        if ($cacheable) {
            static::$_getCache[$query_string] = $this->_data['get'];
            if (\count(static::$_getCache) > 256) {
                unset(static::$_getCache[key(static::$_getCache)]);
            }
        }
    }

    /**
     * Parse post.
     *
     * @return void
     */
    protected function parsePost()
    {

        $body_buffer = $this->rawBody();

        $this->_data['post'] = $this->_data['files'] = array();
        if ($body_buffer === '') {
            return;
        }
        $cacheable = static::$_enableCache && !isset($body_buffer[1024]);
        if ($cacheable && isset(static::$_postCache[$body_buffer])) {
            $this->_data['post'] = static::$_postCache[$body_buffer];
            return;
        }
        $content_type = $this->header('content-type', '');
        if (\preg_match('/boundary="?(\S+)"?/', $content_type, $match)) {
            $http_post_boundary = '--' . $match[1];
            $this->parseUploadFiles($http_post_boundary);
            return;
        }
        if (\preg_match('/\bjson\b/i', $content_type)) {
            $this->_data['post'] = (array) json_decode($body_buffer, true);
        } else {
            \parse_str($body_buffer, $this->_data['post']);
        }
        if ($cacheable) {
            static::$_postCache[$body_buffer] = $this->_data['post'];
            if (\count(static::$_postCache) > 256) {
                unset(static::$_postCache[key(static::$_postCache)]);
            }
        }
    }

    /**
     * Parse upload files.
     *
     * @param $http_post_boundary
     * @return void
     */
    protected function parseUploadFiles($http_post_boundary)
    {
        $http_body = $this->rawBody();
        $http_body = \substr($http_body, 0, \strlen($http_body) - (\strlen($http_post_boundary) + 4));
        $boundary_data_array = \explode($http_post_boundary . "\r\n", $http_body);
        if ($boundary_data_array[0] === '') {
            unset($boundary_data_array[0]);
        }
        $key = -1;
        $files = array();
        foreach ($boundary_data_array as $boundary_data_buffer) {
            list($boundary_header_buffer, $boundary_value) = \explode("\r\n\r\n", $boundary_data_buffer, 2);
            // Remove \r\n from the end of buffer.
            $boundary_value = \substr($boundary_value, 0, -2);
            $key++;
            foreach (\explode("\r\n", $boundary_header_buffer) as $item) {
                list($header_key, $header_value) = \explode(": ", $item);
                $header_key = \strtolower($header_key);
                switch ($header_key) {
                    case "content-disposition":
                        // Is file data.
                        if (\preg_match('/name="(.*?)"; filename="(.*?)"$/i', $header_value, $match)) {
                            $error = 0;
                            $tmp_file = '';
                            $size = \strlen($boundary_value);
                            $tmp_upload_dir = HTTP::uploadTmpDir();
                            if (!$tmp_upload_dir) {
                                $error = UPLOAD_ERR_NO_TMP_DIR;
                            } else {
                                $tmp_file = \tempnam($tmp_upload_dir, 'wondphp.upload.');
                                if ($tmp_file === false || false == \file_put_contents($tmp_file, $boundary_value)) {
                                    $error = UPLOAD_ERR_CANT_WRITE;
                                }
                            }
                            // Parse upload files.
                            $files[$key] = array(
                                'key' => $match[1],
                                'name' => $match[2],
                                'tmp_name' => $tmp_file,
                                'size' => $size,
                                'error' => $error
                            );
                            break;
                        } // Is post field.
                        else {
                            // Parse $_POST.
                            if (\preg_match('/name="(.*?)"$/', $header_value, $match)) {
                                $this->_data['post'][$match[1]] = $boundary_value;
                            }
                        }
                        break;
                    case "content-type":
                        // add file_type
                        $files[$key]['type'] = \trim($header_value);
                        break;
                }
            }
        }

        foreach ($files as $file) {
            $key = $file['key'];
            unset($file['key']);
            $this->_data['files'][$key] = $file;
        }
    }

    /**
     * Create session id.
     *
     * @return string
     */
    protected static function createSessionId()
    {
        return \bin2hex(\pack('d', \microtime(true)) . \pack('N', \mt_rand()));
    }


    /**
    * Checks whether the request is secure or not.
    *
    * This method can read the client protocol from the "X-Forwarded-Proto" header
    * when trusted proxies were set via "setTrustedProxies()".
    *
    * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
    *
    * @return bool
    */
    public function isSecure()
    {
        // if ($this->isFromTrustedProxy() && $proto = $this->getTrustedValues(self::HEADER_X_FORWARDED_PROTO)) {
        //     return \in_array(strtolower($proto[0]), ['https', 'on', 'ssl', '1'], true);
        // }

        $https = $_SERVER['HTTPS'] ?? '';

        return !empty($https) && 'off' !== strtolower($https);
    }

    /**
     * Indicates whether this request originated from a trusted proxy.
     *
     * This can be useful to determine whether or not to trust the
     * contents of a proxy-specific header.
     *
     * @return bool true if the request came from a trusted proxy, false otherwise
     */
    public function isFromTrustedProxy()//todo
    {
        return self::$trustedProxies && IpUtils::checkIp($_SERVER['REMOTE_ADDR'], self::$trustedProxies);
    }
    /**
     * Setter.
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Getter.
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    /**
     * Isset.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Unset.
     *
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    /**
     * __destruct.
     *
     * @return void
     */
    public function __destruct()
    {
        if (isset($this->_data['files'])) {
            \clearstatcache();
            foreach ($this->_data['files'] as $item) {
                if (\is_file($item['tmp_name'])) {
                    \unlink($item['tmp_name']);
                }
            }
        }
    }

    public function __toString()
    {
        return $this->rawHead() ."\r\n" .$this->rawBody();
    }
}
