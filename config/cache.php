<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */


    'default' => env('CACHE_DRIVER', 'auto'),  //auto 

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apcu", "memcached", "redis", 
    */

    'stores' => [

        'apcu' => [
            'driver' => 'apcu',
        ],

        'array__notavailable' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database__notavailable' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file__notavailable' => [
            'driver' => 'file',
            //'path' => storage_path('framework/cache/data'),
        ],

        'memcache' => [
            'driver' => 'memcache',
            'host'=>'127.0.0.1',
            'schema' => '',
            'port'=>'11211',
            'user' => NULL,
            'password' => NULL,
        ],

        'redis' => [
            'driver' => 'redis',
            'host'=>'127.0.0.1',
            'schema' => '',
            'port'=>6379,
         //   'user' => NULL,
          //  'password' => NULL,
        ],

       

       

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis,  cache
    | stores there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', 'amazephp_cache_'),

];
