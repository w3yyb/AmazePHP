<?php
namespace AmazePHP;

use eftec\CacheOne;

class Cache
{
    // use SingletonTrait;

    private $cache;

    public function __construct()
    {

        $config =include BASE_PATH.'/config/cache.php';

        $driver=$config['default'];

        if ($driver=='redis') {
            $redis=$config['stores']['redis'];
            $this->cache=new CacheOne("redis", $redis['host'], $redis['schema'], $redis['port']);
        } elseif ($driver=='apcu') {
            $this->cache=new CacheOne("apcu");
        } elseif ($driver=='memcache') {
            $memcache=$config['stores']['memcache'];
            $this->cache=new CacheOne("memcache", $memcache['host']); // minimum configuration
            //$cache=new CacheOne("memcache","127.0.0.1",11211,'schema'); // complete configuration
        }elseif($driver=='auto') {
        $this->cache=new CacheOne("auto"); // auto select 
        } else {
            throw new Exception("Cache driver not found");
        }






        //todo array ,file

        return $this->cache;
    }

    public function put($key, $value, $ttl = null)
    {
        return $this->cache->set("group",$key, $value, $ttl);
    }
    public function get($key)
    {
        return $this->cache->get("group",$key);
    }
}
