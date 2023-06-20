# AmazePHP
## About AmazePHP  
A good choice for starting a PHP project, it can be used for web and API development. It only takes one minute to start a project. It has super high performance and a very easy-to-use development experience.
There are no complicated concepts, so it has the lowest learning curve. 

## Features:  
- Configuration  
- Env Vars  
- Routing    
- Requests 
- Controllers 
- Middleware 
- Facades 
- Container 
- Database  
- Http Client  
- Logging   
- Templates 
- Error Handling  
- Cache 
- Session 
- Cookie 
- URL Generation 
- CSRF Protection 


## Installation:    
```
composer create-project amazephp/amazephp
or use git clone:
git clone https://github.com/w3yyb/AmazePHP.git  

cd AmazePHP 

composer install  
```
 ## Run
 ```
 cd public/  

 php -S localhost:9080 server.php  
 ```
 Open http://localhost:9080  in your browser.

 ## Requirements:  
 php 8.1+  

 ## Directory Structure  

 ### The App Directory  
 The app directory contains the core code of your application. We'll explore this directory in more detail soon; however, almost all of the classes in your application will be in this directory.  
 ### The config Directory  
 The config directory, as the name implies, contains all of your application's configuration files.  Include the route config file.
  ### The helper Directory  
The helper functions in it.
### The AmazePHP Directory
The framework core directory, include some lib class. And you can put  your class file in it.
### The public Directory
The public directory contains the index.php file, which is the entry point for all requests entering your application and configures autoloading. This directory also houses your assets such as images, JavaScript, and CSS.
### The cache Directory
The cache directory contains your cache files, include log files.
### The template Directory
The template directory contains your html template files.

 # Usage 
## Configuration 
 ### get config  
 ``` 
  config('app'); //will read config/app.php,  app.php return an array.  

  config('app.url')// == config('app')['url'];  

  // Retrieve a default value if the configuration value does not exist...
  $value = config('app.timezone', 'Asia/Seoul');
  ``` 
  
 ### set config 
 To set configuration values at runtime, pass an array to the config function:
 ``` 
config(['app.timezone' => 'America/Chicago']);
``` 
 
 
## Cache  
### get cache 
```
$value = cache('key');
```
### set cache 
```
cache(['key' => 'value'], 10);// Expires after 10 seconds
``` 
### view 
The template engine uses `BladeOne`, a template engine similar to the laravel `blade`, click here https://github.com/EFTEC/BladeOne/wiki/BladeOne-Manual view the BladeOne manual. 
```
echo view('greeting', ['name' => 'James']);
``` 
The first parameter is the template name, i.e. `template/greeting.blade.php` , and the second parameter is the variable passed into the template. 
###  get Environment configuration 
```
env('key');

env('key','default'); 
```
The second value passed to the env function is the "default value". This value will be returned if no environment variable exists for the given key.

### Logging
```
logger('some msg');//error log
```
```
logger('some msg','warning'); //warning log | support:emergency ,alert ,critical ,error ,warning ,notice ,info ,debug 
```
### Routing 
see config/route.php
```
  [
        ['GET'], 
        '/',  
        [App\Controllers\Index::class, 'index'],
        ['routename'],
        'middleware'=>[App\Middleware\a2Middleware::class,App\Middleware\b2Middleware::class],
  ],
```
The first line is the HTTP request method, which supports HEAD, GET, POST, PUT, PATCH, DELETE. `['POST,GET']` means that both POST and GET are supported. `['*']` indicates that all HTTP methods are supported.  

The second line represents the path, like `/users/{uid}/posts/[{pid}][/]`:  in curly braces is variable parameters , optional parameters in brackets, i.e. parameters that have not passed in the URL, `[/]` for remove the trailing slash.  

The third line indicates PHP callbacks, support for object methods, static methods of classes, anonymous functions, functions, etc. 

The fourth line is optional and indicates the name of the named route. 

The middleware key  is optional  register routing middleware.

### Requests 
 AmazePHP\Request class provides an object-oriented way to interact with the current HTTP request being handled by your application as well as retrieve the input, cookies, and files that were submitted with the request.
 #### usage
 ```
 $input = request()->all();
$name = request()->input('name');
$value = request()->cookie('name');
$value = request()->header('X-Header-Name');
$method = request()->method();
request()->host();
$url = request()->url();
$urlWithQueryString = request()->fullUrl();
$uri = request()->path();
if (request()->is('admin/*')) {
    // ...
}

$input = request()->only(['username', 'password']);
 
$input = request()->except(['credit_card']);

  $file = request()->file('upload');
        if ($file && $file->isValid()) {
            $file->move(PUBLIC_PATH.'/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }

 ```
 More usage see AmazePHP/src/Request.php.
### Controllers 
Instead of defining all of your request handling logic as closures in your route files, you may wish to organize this behavior using "controller" classes. Controllers can group related request handling logic into a single class. For example, a `UserController` class might handle all incoming requests related to users, including showing, creating, updating, and deleting users. By default, controllers are stored in the `app/Controllers` directory.
#### Writing Controllers 
In `app/Controllers ` dir, you can write some controllers, for example:
```
<?php
namespace App\Controllers;

class Index
{
    public function index()
    {
        echo 'Hello AmazePHP!';
    }
}
```
Once you have written a controller class and method, you may define a route to the controller method like so:
```
 [
        ['GET'],
        '/',
        [new App\Controllers\Index, 'index']
 ],
```
When an incoming request matches the specified route URI, the index method on the App\Controllers\Index class will be invoked and the route parameters will be passed to the method. 
### Middleware 
Middleware, also known as HTTP middleware, is mainly used to modify or filter HTTP requests or responses.All of these middleware are located in the app/Middleware directory.  
 
Middleware is divided into before middleware and after middleware.Before middleware is mainly used to modify HTTP requests. After middleware is mainly used to modify HTTP responses.

```
Request->Before middleware->Actual action->After middleware->Response
```
#### Defining Middleware 
The main difference between before middleware and after middleware is where the code is executed. 
In app/Middleware directory:
##### Defining Before Middleware 
Create such as  bMiddleware.php
```
<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;

class bMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {
        
        //Perform some logic here

        return $next($object);
    }

}
?>

```
##### Defining After Middleware 
Create such as  aMiddleware.php

```
<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;

class aMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {

        $response = $next($object);
         //Perform some logic here

        return $response;
    }

}
?>

```
#### Registering Global Middleware
In config/middleware.php,write the following:
```
return [
    App\Middleware\aMiddleware::class,
    App\Middleware\bMiddleware::class,
];
```
#### Register routing middleware 
See Routing.

### Facades 
The façade provides a static call interface for the (dynamic) classes of the framework's core class library.Enables you to statically call dynamic class methods .
#### sample:
In App\Controllers\Index Controller:

```
<?php
namespace App\Controllers;
use AmazePHP\Facade\Request;
class Index  
{
    public function index()
    {
        echo Request::url();//call Request.php url method statically. Same as calling  Request->url().
    }
}
```
All façades of the system are placed in the AmazePHP/src/Facade directory.


### Container 
 Container is a powerful tool for managing class dependencies and performing dependency injection.

 #### usage:
For example:
```
<?php
namespace App\Controllers;
use AmazePHP\Request;
class Foo  
{
    public function bar(Request $request, $id)
    {
    echo $request->url();
    }
}
?>
```
The bar method depends on the Request class.You can put the Request $request in the bar parameters.Framework will auto call the Request class,so you can use the Request class method:e.g. ` $request->url() ` .  
Scenarios that support the use of dependency injection include (but are not limited to): 
- Controller   method;
- closure definition of a route;
- Middleware execution methods;
    
### http client 
```
$response= httpGet('http://httpbin.org/get');
$response= httpGet('http://httpbin.org/get',['headername'=>'headervalue']);
$response= httpHead('http://httpbin.org/get',['headername'=>'headervalue']);
$response= httpDelete('http://httpbin.org/delete',['headername'=>'headervalue']);
$response= httpPost('http://httpbin.org/post',['senddataname'=>'senddatavalue']);
$response= httpPut('http://httpbin.org/put',['senddataname'=>'senddatavalue']);
$response= httpPatch('http://httpbin.org/patch',['senddataname'=>'senddatavalue']);
```
$response is an array containing status_code, header, and body data.  

The function parameters are as follows: 
```
httpGet($url,$header = [])
httpHead($url,$header = [])
httpDelete($url,$header = [])

httpPost($url, $data, $isJson = true,$method='POST',$header = [])
httpPut($url, $data, $isJson = true,$method='PUT',$header = [])
httpPatch($url, $data, $isJson = true,$method='PATCH',$header = [])
```
## session 
Session is closed by default, if you want to open, please change the SESSION_ENABLE in the .env file to true.
### set session
```
session(["name" => "value"]);
```        
### get session
```
$value = session('name')
```
## cookie 
### get cookie
```
$value = $_COOKIE['name'];
```
### set cookie 
```
cookie('name','value',86400); // 86400 seconds
```

###  Database
The database component is using `PdoOne`, a database access object wrapper for PHP and PDO. Click https://github.com/EFTEC/PdoOne to see how to use it. 

Below is an example of how to use it in a simple way.  

select: 
```
$results = db()->select("*")->from('users')->where("name like '%test%'")->toList();

print_r($results);
``` 
use Raw Sql: 
```
$sql='select * from users where id=1';

$pdoStatement=db()->runRawQuery($sql,[],false);  // [] are the parameters

print_r($pdoStatement->fetchAll());
```
insert: 
```
db()->insert("users"
    ,['name','email','password']
    ,['kevin','email@email.com','123456']);
```
update: 
```
db()->update("users"
,['name'=>'Captain-Crunch','email'=>'mail@mail.com'] // set
,['id'=>6]); // where
```
delete: 
```
db()->delete("users"
  ,['id'=>6]); // where
```
    
### URL
#### Accessing The Current URL 
```
// Get the current URL without the query string...
echo url()->current();
 
// Get the current URL including the query string...
echo url()->full();
 
// Get the full URL for the previous request...
echo url()->previous(); 
```
#### Generating URLs
```
echo url("/posts/{$post->id}"); // http://example.com/posts/1
```
#### URLs For Named Routes 
```
[
        ['GET'],
        '/hello/{id}/foo/{sid}',
        [new App\Foo, 'bar'],
        'nameroute1'//Named Route
],

echo   route('nameroute1', ['id' => 1, 'sid' => 2]);

// http://example.com/hello/1/foo/2
``` 
## CSRF Protection 
If you want to use it, first enable session. 

Cross-site request forgeries are a type of malicious exploit whereby unauthorized commands are performed on behalf of an authenticated user. Thankfully, AmazePHP makes it easy to protect your application from cross-site request forgery (CSRF) attacks.

AmazePHP automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the person actually making the requests to the application. Since this token is stored in the user's session and changes each time the session is regenerated, a malicious application is unable to access it. 

The current session's CSRF token can be accessed  via the `csrf_token` helper function:
```
$token = csrf_token();
```
Anytime you define a "POST", "PUT", "PATCH", or "DELETE" HTML form in your application, you should include a hidden CSRF `_token` field in the form so that the CSRF protection middleware can validate the request. For convenience, you may use the `@csrf `Blade directive to generate the hidden token input field:
```
<form method="POST" action="/profile">
    @csrf
 
    <!-- Equivalent to... -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
</form>
```
### X-CSRF-TOKEN 
In addition to checking for the CSRF token as a POST parameter, the `lib\VerifyCsrfToken`   will also check for the `X-CSRF-TOKEN` request header. You could, for example, store the token in an HTML `meta` tag:
```
<meta name="csrf-token" content="{{ csrf_token() }}">
```
Then, you can instruct a library like jQuery to automatically add the token to all request headers. This provides simple, convenient CSRF protection for your AJAX based applications using legacy JavaScript technology:
```
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```
### X-XSRF-TOKEN 
AmazePHP stores the current CSRF token in an encrypted `XSRF-TOKEN` cookie that is included with each response generated by the framework. You can use the cookie value to set the `X-XSRF-TOKEN` request header.

This cookie is primarily sent as a developer convenience since some JavaScript frameworks and libraries, like Angular and Axios, automatically place its value in the `X-XSRF-TOKEN` header on same-origin requests.
## Test
```
./phpunit --bootstrap vendor/autoload.php tests
./phpunit --bootstrap vendor/autoload.php tests  --display-warnings
./phpunit --bootstrap vendor/autoload.php tests  --display-deprecations
```

## Benchmark 
AmazePHP is about 9x faster than Laravel in `hello world` benchmark.  
laravel :     2900 rps.  
AmazePHP: 23000 rps.  
Both open debugs, and Laravel uses the Array Session Driver. 