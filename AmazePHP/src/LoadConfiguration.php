<?php

namespace AmazePHP;
 

use Exception as Exception;
use SplFileInfo as SplFileInfo;

class LoadConfiguration
{
    use SingletonTrait;
    public $repo;
    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function __construct( )
    {
        $apcu_key="config";
        $items = [];
        // $app=app();


        // if (apcu_exists($apcu_key)) {
        //     $items= apcu_fetch($apcu_key);
        //      $loadedFromCache = true;

        // }

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        // if (is_file($cached = $app->getCachedConfigPath())) {
        //     $items = require $cached;

        //     $loadedFromCache = true;
        // }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.

        $this->repo=$repository = new ConfigRepository($items);


        $config_path = BASE_PATH . '/config';

        if (! isset($loadedFromCache)) {
            $this->loadConfigurationFiles($config_path, $repository);
           //apcu_store($apcu_key, $repository->all(), 6);

        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        // $app->detectEnvironment(function () use ($config) {
        //     return $config->get('app.env', 'production');
        // });

        date_default_timezone_set($repository->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');
    }

    public function get($key,$val=null){

        return $this->repo->get($key,$val);

    }


    public function set( array $key){

        return $this->repo->set($key);

    }

    /**
     * Load the configuration items from all of the files.
     *
 
     * @return void
     *
     * @throws \Exception
     */
    protected function loadConfigurationFiles( $config_path,$repository)
    {

        $files = $this->getConfigurationFiles($config_path);

        if (! isset($files['app'])) {
          //  throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles( $config_path)
    {
        $files = [];

        $configPath = realpath($config_path);


        foreach ( glob($configPath.'/*.php') as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file, '.php')] = $file;
        }

        ksort($files, SORT_NATURAL);
        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getNestedDirectory( $file, $configPath)
    {
        $directory = dirname($file);

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }
}
