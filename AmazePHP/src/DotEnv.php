<?php
namespace AmazePHP;

class DotEnv
{

    /**
     * Convert true and false to booleans, instead of:
     *
     * VARIABLE=false -> ['VARIABLE' => 'false']
     *
     * it will be
     *
     * VARIABLE=false -> ['VARIABLE' => false]
     *
     * default = true
     */
    const PROCESS_BOOLEANS =false;

    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;

    /**
     * Configure the options on which the parsed will act
     *
     * @var array
     */
    protected $options = [];

    public function __construct(array $options = [])
    {
        $GLOBALS['dotenv'] =1;
        // define('BASE_PATH', dirname(__DIR__));
        $path=BASE_PATH.'/.env';
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        $this->path = $path;
        $this->processOptions($options);
        $this->load();
    }

    private function processOptions(array $options) : void
    {
        $this->options = array_merge([
            'PROCESS_BOOLEANS' => static::PROCESS_BOOLEANS
        ], $options);
    }

    /**
     * Processes the $path of the instances and parses the values into $_SERVER and $_ENV, also returns all the data that has been read.
     * Skips empty and commented lines.
     */
    public function load() : void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            $line  =trim($line);

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = $this->processValue($value);

            if (!\array_key_exists($name, $_SERVER) && !\array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    private function processValue(string $value)
    {
        $trimmedValue = trim($value);

        if (!empty($this->options['PROCESS_BOOLEANS'])) {
            $loweredValue = strtolower($trimmedValue);

            $isBoolean = \in_array($loweredValue, ['true', 'false'], true);

            if ($isBoolean) {
                return $loweredValue === 'true';
            }
        }

        return $trimmedValue;
    }
}

// $dotenv = new DotEnv();

// echo env('APP_KEY');
