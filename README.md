# AmazePHP
## About AmazePHP  
A good choice for starting a PHP  project, it can be used for web and API development.   
It only takes ONE minute to start a project.  
It has super high performance, and a very easy to use development experience.  
Native PHP first ，Lowest learning curve.  

## Features:  
- Configuration  
- Env Vars  
- Routing    
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


## install:    
```
git clone https://github.com/w3yyb/AmazePHP.git  

cd AmazePHP 

composer install  
```
 ## run
 ```
 cd public/  

 php -S localhost:9080 server.php  
 ```
 Open http://localhost:9080  in your browser.

 ## requirements:  
 php 8.1  

 ## Directory Structure  

 ### The App Directory  
 The app directory contains the core code of your application. We'll explore this directory in more detail soon; however, almost all of the classes in your application will be in this directory.  
 ### The config Directory  
 The config directory, as the name implies, contains all of your application's configuration files.  Include the route config file.
  ### The helper Directory  
The helper functions in it.
### The lib Directory
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
```
echo view('greeting', ['name' => 'James']);
``` 
The first parameter is the template name, i.e. `template/greeting.blade.php` , and the second parameter is the variable passed into the template. 
###  get env 
```
env('key');

env('key','default'); 
```
The second value passed to the env function is the "default value". This value will be returned if no environment variable exists for the given key.

### Logging
```
logs('some msg');//error log
```
```
logs('some msg','warning'); //warning log | support:emergency ,alert ,critical ,error ,warning ,notice ,info ,debug 
```
### Routing 
see config/route.php
```
  [
        ['GET'], 
        '/',  
        [new App\Index, 'index'],
        ['routename']
  ],
```
The first line is the HTTP request method, which supports HEAD, GET, POST, PUT, PATCH, DELETE. `['POST,GET']` means that both POST and GET are supported. `['*']` indicates that all HTTP methods are supported.  

The second line represents the path, like `/users/{uid}/posts/[{pid}][/]`:  in curly braces is variable parameters , optional parameters in brackets, i.e. parameters that have not passed in the URL, `[/]` for remove the trailing slash.  

The third line indicates PHP callbacks, support for object methods, static methods of classes, anonymous functions, functions, etc. 

The fourth line is optional and indicates the name of the named route. 


    
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
select 
```
$results = db()->select("*")->from('users')->where("name like '%test%'")->toList();

print_r($results);
``` 
use Raw Sql 
```
$sql='select * from users where id=1';

$pdoStatement=db()->runRawQuery($sql,[],false);  // [] are the parameters

print_r($pdoStatement->fetchAll());
```
insert 
```
db()->insert("users"
    ,['name','email','password']
    ,['kevin','email@email.com','123456']);
```
update 
```
db()->update("users"
,['name'=>'Captain-Crunch','email'=>'mail@mail.com'] // set
,['id'=>6]); // where
```
delete 
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