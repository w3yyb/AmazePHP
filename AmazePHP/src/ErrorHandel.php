<?php
namespace AmazePHP;

// use Monolog\Level;
// use Monolog\Logger;
// use Monolog\Handler\StreamHandler;

class ErrorHandel
{
    private array $levels = [
        \E_DEPRECATED => "Deprecated",
        \E_USER_DEPRECATED => "User Deprecated",
        \E_NOTICE => "Notice",
        \E_USER_NOTICE => "User Notice",
        \E_STRICT => "Runtime Notice",
        \E_WARNING => "Warning",
        \E_USER_WARNING => "User Warning",
        \E_COMPILE_WARNING => "Compile Warning",
        \E_CORE_WARNING => "Core Warning",
        \E_USER_ERROR => "User Error",
        \E_RECOVERABLE_ERROR => "Catchable Fatal Error",
        \E_COMPILE_ERROR => "Compile Error",
        \E_PARSE => "Parse Error",
        \E_ERROR => "Error",
        \E_CORE_ERROR => "Core Error",
    ];
    private array $loggers = [
        \E_DEPRECATED => [null, "LogLevel::INFO"],
        \E_USER_DEPRECATED => [null, "LogLevel::INFO"],
        \E_NOTICE => [null, "LogLevel::WARNING"],
        \E_USER_NOTICE => [null, "LogLevel::WARNING"],
        \E_STRICT => [null, "LogLevel::WARNING"],
        \E_WARNING => [null, "LogLevel::WARNING"],
        \E_USER_WARNING => [null, "LogLevel::WARNING"],
        \E_COMPILE_WARNING => [null, "LogLevel::WARNING"],
        \E_CORE_WARNING => [null, "LogLevel::WARNING"],
        \E_USER_ERROR => [null, "LogLevel::CRITICAL"],
        \E_RECOVERABLE_ERROR => [null, "LogLevel::CRITICAL"],
        \E_COMPILE_ERROR => [null, "LogLevel::CRITICAL"],
        \E_PARSE => [null, "LogLevel::CRITICAL"],
        \E_ERROR => [null, "LogLevel::CRITICAL"],
        \E_CORE_ERROR => [null, "LogLevel::CRITICAL"],
    ];
    private Logger $log;
    public function __construct()
    {
        set_error_handler([$this, "errorHandler"]);
        set_exception_handler([$this, "exceptionHandler"]);
        register_shutdown_function([$this, "shutdownFunction"]);

        // $this->log= new Logger('errorlog');
        // $logfile= BASE_PATH.'/cache/error.log';
        // $this->log->pushHandler(new StreamHandler($logfile, Level::Warning));
    }

    public function shutdownFunction()
    {
        $lasterror = error_get_last();

        if ($lasterror == null) {
            return;
        }
        switch ($lasterror['type']) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_PARSE:
                $error = "[SHUTDOWN] lvl:" . $lasterror['type'] . " | msg:" . $lasterror['message'] . " | file:" . $lasterror['file'] . " | ln:" . $lasterror['line'];
                if (env('APP_DEBUG')) {
                    print_r($error);
                }
                $this->logError($error, "error");

                //mylog($error, "fatal");
        }
    }

    public function exceptionHandler($exception)
    {
        $errorinfo = "Exception: " . $exception->getMessage() . "  at  Line:" . $exception->getLine() . "  in   File:" . $exception->getFile();
        $errorinfostr = $exception->getMessage();

        if ($errorinfostr == "404 Not Found") {
            //"404 Not Found" should throw  by your router
            http_response_code(404);


            if (request()->expectsJson() || request()->isJson()) {
                echo json_encode(['code'=>404,   'message'=>'error'  ,'content' => $errorinfo]);
              }else{
                include "../404.html";
              }

          
        } elseif ($errorinfostr == "405 Not Allowed") {
            http_response_code(405);


            if (request()->expectsJson() || request()->isJson()) {
                echo json_encode(['code'=>405,   'message'=>'error'  ,'content' => $errorinfostr]);
              }else{
                echo '<html>
            <head>
               <title>
                  405 Not Allowed
               </title>
         </head>
            <body>
               <center>
                  <h1>
                     405 Not Allowed
                  </h1>
         </center>
               <hr>
               <center>
                   
               </center>
         </body>
         </html>';
              }


          
        } else {
            http_response_code(500);
            $this->logError($errorinfo, "error");
            if (!env('APP_DEBUG')) {
                $errorinfo = "500 Internal Server Error";
            }

            if (request()->expectsJson() || request()->isJson()) {
              echo json_encode(['code'=>500,   'message'=>'error'  ,'content' => $errorinfo]);
            }else{
                include "../500.html"; //echo $errorinfo in 500.html
            }
        }
    }

    public function errorHandler($severity, $message, $file, $line)
    {
        $full_msg=$message . "  at  Line:" . $line . "  in   File:" . $file;
        throw new \ErrorException($this->friendlyErrorType($full_msg, $severity) . ":" . $message, 0, $severity, $file, $line);
    }

    public function friendlyErrorType($error, $type)
    {
        switch ($type) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                $this->logError($error, "error");
                return 'error';
                break;
            case E_USER_DEPRECATED:
            case E_DEPRECATED:
                $this->logError($error, "info");
                return "info";
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                $this->logError($error, "warning");
                return 'warning';
                break;
            default:
                $this->logError($error, "warning");
        }
        return "";
    }

    public function logError($err, $level)
    {
        // $this->log->$level($err);
        logger($err,$level);
    }

    public function __destruct()
    {
        restore_error_handler();
        restore_exception_handler();
    }
}
