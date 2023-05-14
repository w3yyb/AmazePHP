<?php

// namespace WondPHP\Http\Protocols;
// use Illuminate\Support\Arr;
// use Illuminate\Support\Str;
/**
 * Class Session
 *
 */
class Session
{
    use SingletonTrait;
    /**
     * Session andler class which implements SessionHandlerInterface.
     *
     * @var string
     */
    protected static $_handlerClass = 'FileSessionHandler';

    /**
     * Parameters of __constructor for session handler class.
     *
     * @var null
     */
    protected static $_handlerConfig = null;

    /**
     * Session.gc_probability
     *
     * @var int
     */
    protected static $_sessionGcProbability = 1;

    /**
     * Session.gc_divisor
     *
     * @var int
     */
    protected static $_sessionGcDivisor = 1000;

    /**
     * Session.gc_maxlifetime
     *
     * @var int
     */
    protected static $_sessionGcMaxLifeTime = 1440;

    /**
     * Session handler instance.
     *
     * @var \SessionHandlerInterface
     */
    protected static $_handler = null;

    /**
     * Session data.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Session changed and need to save.
     *
     * @var bool
     */
    protected $_needSave = false;

    /**
     * Session id.
     *
     * @var null
     */
    protected $_sessionId = null;
    protected $_buffer = '';
    protected static $_enableCache = true;
    protected static $_headerCache = array();

    /**
     * Session constructor. 
     *
     * @param $session_id
     */
    public function __construct()
    {

        // var_dump($this->_sessionId);

        $session_id = session_id();
        if (empty($session_id)) {
            $session_id =$this->sessionId();
        }
        static::checkSessionId($session_id);
        if (static::$_handler === null) {
            static::handlerClass(config('session')['handler'], config('session')['config'][config('session')['type']]);//  new add 
            static::initHandler();
        }
        $this->_sessionId = $session_id;
        if ($data = static::$_handler->read($session_id)) {
            $this->_data = \unserialize($data);
        }

        $this->_data=   array_merge($_SESSION ?? [], $this->_data);
    }


    function sessionId()
    {


        // return  md5(uniqid('', true));

        if (!isset($this->_data['sid'])) {
            $session_name = 'PHPSID';
            // $sid = $this->cookie($session_name);
            $sid =  $_COOKIE[$session_name] ?? null;


            if ($sid === '' || $sid === null) {
                if (0) {
                    echo('Request->session() fail, header already send');
                    return false;
                }
                // $sid = static::createSessionId();
                $sid =  session_create_id();
                $cookie_params = \session_get_cookie_params();
                $lifetime =config('session.lifetime') ??$cookie_params['lifetime'] ;

                $header= array($session_name . '=' . $sid
                    . (empty($cookie_params['domain']) ? '' : '; Domain=' . $cookie_params['domain'])
                    . (empty($lifetime) ? '' : '; Max-Age=' . ($lifetime *60))
                    . (empty($cookie_params['path']) ? '' : '; Path=' . $cookie_params['path'])
                    . (empty($cookie_params['samesite']) ? '' : '; SameSite=' . $cookie_params['samesite'])
                    . (!$cookie_params['secure'] ? '' : '; Secure')
                    . (!$cookie_params['httponly'] ? '' : '; HttpOnly'));
                header("Set-Cookie: $header[0]");
            }
            $this->_data['sid'] = $sid;
        }
        return $this->_data['sid'];
    }


    public function cookie($name = null, $default = '')
    {
        if (!isset($this->_data['cookie'])) {
             
            \parse_str(\str_replace('; ', '&', $this->header('cookie')), $this->_data['cookie']);
        }
        if ($name === null) {
            return $this->_data['cookie'];
        }
        return isset($this->_data['cookie'][$name]) ? $this->_data['cookie'][$name] : $default;
    }

    public function header($name = null, $default = '')
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

    public function rawHead()
    {
        if (!isset($this->_data['head'])) {
            $this->_data['head'] = \strstr($this->_buffer, "\r\n\r\n", true);
        }
        return $this->_data['head'];
    }

    /**
     * Get session id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sessionId;
    }

    /**
     * Get session.
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        if (isset($this->_data[$name])) {
            $_SESSION[$name]=$this->_data[$name];
        }
        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }

    /**
     * Store data in the session.
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->_data[$name] = $value;
        $this->_needSave = true;
    }

    /**
     * Delete an item from the session.
     *
     * @param $name
     */
    public function delete($name)
    {
        unset($this->_data[$name]);
        unset($_SESSION[$name]);

        $this->_needSave = true;
    }

      /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token()
    {
        return $this->get('_token');
    }

    /**
     * Retrieve and delete an item from the session.
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function pull($name, $default = null)
    {
        $value = $this->get($name, $default);
        $this->delete($name);
        return $value;
    }

    /**
     * Store data in the session.
     *
     * @param $key
     * @param null $value
     */
    public function put($key, $value = null)
    {
        if (!\is_array($key)) {
            $_SESSION[$key] =$value;
            $this->set($key, $value);
            return;
        }

        foreach ($key as $k => $v) {
            $_SESSION[$k] =$v;
            $this->_data[$k] = $v;
        }
        $this->_needSave = true;
    }

    /**
     * Remove a piece of data from the session.
     *
     * @param $name
     */
    public function forget($name)
    {
        if (\is_scalar($name)) {
            $this->delete($name);
            return;
        }
        if (\is_array($name)) {
            foreach ($name as $key) {
                unset($_SESSION[$key]);
                unset($this->_data[$key]);
            }
        }
        $this->_needSave = true;
    }

    /**
     * Retrieve all the data in the session.
     *
     * @return array
     */
    public function all()
    {
        foreach ($this->_data as $key => $value) {
            $_SESSION[$key] =$value;
        }
        return $this->_data;
    }

    /**
     * Remove all data from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->_needSave = true;
        $this->_data = array();
        $_SESSION = [];
    }

    /**
     * Determining If An Item Exists In The Session.
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_data[$name]);
    }

      /**
     * Regenerate the CSRF token value.
     *
     * @return void
     */
    public function regenerateToken()
    {
        $this->put('_token', Str::random(40));
    }

    /**
     * To determine if an item is present in the session, even if its value is null.
     *
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        return \array_key_exists($name, $this->_data);
    }

    /**
     * Save session to store.
     *
     * @return void
     */
    public function save()
    {
        $this->ageFlashData();

        // Store a copy so we can restore the bags in case the session was not left empty
        $session = $_SESSION ??[];
        if ($this->_needSave) {
            if (empty($this->_data)) {
                static::$_handler->destroy($this->_sessionId);
            } else {
                static::$_handler->write($this->_sessionId, \serialize($this->_data));
            }
           
            try {
                session_write_close();
            } finally {
                restore_error_handler();

                // Restore only if not empty
                if ($_SESSION) {
                    $_SESSION = $session;
                }
            }
        }
        $this->_needSave = false;
    }


    /**
     * Push a value onto a session array.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key, []);

        $array[] = $value;

        $this->put($key, $array);
    }


    /**
     * Age the flash data for the session.
     *
     * @return void
     */
    public function ageFlashData()
    {
        $this->forget($this->get('_flash.old', []));

        $this->put('_flash.old', $this->get('_flash.new', []));

        $this->put('_flash.new', []);
    }


    /**
     * Get the requested item from the flashed input array.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getOldInput($key = null, $default = null)
    {
        return Arr::get($this->get('_old_input', []), $key, $default);
    }


    /**
     * Flash a key / value pair to the session.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function flash(string $key, $value = true)
    {
        $this->put($key, $value);

        $this->push('_flash.new', $key);

        $this->removeFromOldFlashData([$key]);
    }

    /**
     * Flash a key / value pair to the session for immediate use.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function now($key, $value)
    {
        $this->put($key, $value);

        $this->push('_flash.old', $key);
    }

     /**
     * Set the "previous" URL in the session.
     *
     * @param  string  $url
     * @return void
     */
    public function setPreviousUrl($url)
    {
        $this->put('_previous.url', $url);
    }

    /**
     * Reflash all of the session flash data.
     *
     * @return void
     */
    public function reflash()
    {
        $this->mergeNewFlashes($this->get('_flash.old', []));

        $this->put('_flash.old', []);
    }

    /**
     * Reflash a subset of the current flash data.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function keep($keys = null)
    {
        $this->mergeNewFlashes($keys = is_array($keys) ? $keys : func_get_args());

        $this->removeFromOldFlashData($keys);
    }

    /**
     * Merge new flash keys into the new flash array.
     *
     * @param  array  $keys
     * @return void
     */
    protected function mergeNewFlashes(array $keys)
    {
        $values = array_unique(array_merge($this->get('_flash.new', []), $keys));

        $this->put('_flash.new', $values);
    }

    /**
     * Remove the given keys from the old flash data.
     *
     * @param  array  $keys
     * @return void
     */
    protected function removeFromOldFlashData(array $keys)
    {
        $this->put('_flash.old', array_diff($this->get('_flash.old', []), $keys));
    }

    /**
     * Flash an input array to the session.
     *
     * @param  array  $value
     * @return void
     */
    public function flashInput(array $value)
    {
        $this->flash('_old_input', $value);
    }

    /**
     * Init.
     *
     * @return void
     */
    public static function init()
    {
        if ($gc_probability = \ini_get('session.gc_probability')) {
            self::$_sessionGcProbability = (int)$gc_probability;
        }

        if ($gc_divisor = \ini_get('session.gc_divisor')) {
            self::$_sessionGcDivisor = (int)$gc_divisor;
        }

        if ($gc_max_life_time = \ini_get('session.gc_maxlifetime')) {
            self::$_sessionGcMaxLifeTime = (int)$gc_max_life_time;
        }
    }

    /**
     * Set session handler class.
     *
     * @param null $class_name
     * @param null $config
     * @return string
     */
    public static function handlerClass($class_name = null, $config = null)
    {
        if ($class_name) {
            static::$_handlerClass = $class_name;
        }
        if ($config) {
            static::$_handlerConfig = $config;
        }
        return static::$_handlerClass;
    }

    /**
     * Init handler.
     *
     * @return void
     */
    protected static function initHandler()
    {
        if (static::$_handlerConfig === null) {
            static::$_handler = new static::$_handlerClass();
        } else {
            static::$_handler = new static::$_handlerClass(static::$_handlerConfig);
        }
    }

    /**
     * Try GC sessions.
     *
     * @return void
     */
    public function tryGcSessions()
    {
        if (\rand(1, static::$_sessionGcDivisor) > static::$_sessionGcProbability) {
            return;
        }
        static::$_handler->gc(static::$_sessionGcMaxLifeTime);
    }

    /**
     * __destruct.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->save();
        $this->tryGcSessions();
    }

    /**
     * Check session id.
     *
     * @param $session_id
     */
    protected static function checkSessionId($session_id)
    {
        if (!\preg_match('/^[a-zA-Z0-9]+$/', $session_id)) {
            throw new SessionException("session_id $session_id is invalid");
        }
    }
}

/**
 * Class SessionException
 *
 */
class SessionException extends \RuntimeException
{
}

// Init session.
Session::init();
