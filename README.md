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


## install:    
git clone https://github.com/w3yyb/AmazePHP.git  
composer install  

 ## run
 ```
 cd public/  
 php -S localhost:9080 server.php  
 ```
 open http://localhost:9080  in your browser.

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

 # usage 
## configs
 ### get config  
 ``` 
  config('app'); //will read config/app.php  app.php is return an array.  
  config('app.url')// == config('app')['url'];  
  ``` 
  
 ### set config 
 ``` 
 config(['sample.book' =>'6666']);
``` 
``` 
 config(['sample.users' =>[
        ['a'=>4],
        ['b'=>5],
        ['c'=>6],
 ]]);
``` 
 ``` 
 config(['sample' =>[
            ['a'=>4],
            ['b'=>5],
            ['c'=>6],
  ]]);
 ``` 
## cache  
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
echo view('greeting', ['name' => 'James']);//  template/greeting.blade.php   ['name' => 'James'] is the variable passed.
``` 
###  get env
```
env('key');
env('key','default');
```

### logs
```
logs('some msg');//error log
```
```
logs('some msg','warning'); //warning log | support:emergency ,alert ,critical ,error ,warning ,notice ,info ,debug 
```
### routing 
see config/route.php
```
  [
        ['GET'],
        '/',
        [new App\Index, 'index'],//object method
    ],
```
```
    [
        ['POST,GET'],
        '/hello',
        function () {
            echo 'Hello AmazePHP!';
        },
    ],
```
```
    [
        ['*'],
        '/users/{uid}/posts/[{pid}]',
        function ($uid, $pid = 99) {
            var_dump($uid, $pid);
        },
    ],
```
```
  [
        ['GET'],
        '/a/{uid}/b/{pid}',
        ['App\myclass', 'say_hello'],//static method
    ],
```
```
 [
        ['GET'],
        '/hello2',
        'callbackFunction',
    ],
```
```
     [
        ['GET'],
        '/tool-{id}[/]',//Remove the trailing slash
        [new App\Index, 'tool'],
    ],
```
```
   [
        ['GET'],
        '/hello3/{id}/sss/{sid}',
        [new App\Foo, 'bar'],////object, method
        'nameroute1'//named route

    ],
```
    
### http client 
```
        $a= httpGet('http://httpbin.org/get');
        $a= httpGet('http://httpbin.org/get',['aaa'=>'bbb']);
        $a= httpHead('http://httpbin.org/get',['aaa'=>'bbb']);
        $a= httpDelete('http://httpbin.org/delete',['aaa'=>'bbb']);
        $a= httpPost('http://httpbin.org/post',['aaa'=>'bbb']);
        $a= httpPut('http://httpbin.org/put',['aaa'=>'bbb']);
        $a= httpPatch('http://httpbin.org/patch',['aaa'=>'bbb']);
```
## session 
### set session
```
session([
            "name" => "value",
        ]);
```        
### get session
```
echo session('name')
```
## cookie 
### get cookie
```
echo $_COOKIE['name'];
```
### set cookie 
```
cookie('name','value',86400); // 86400 seconds
```

###  database
```
$results = db()->select("*")->from('users')->where("name like '%test%'")->toList();

var_dump($results);
``` 
```
$sql='select * from users where id=1';
$pdoStatement=db()->runRawQuery($sql,[],false);  // [] are the parameters
var_dump($pdoStatement->fetchAll());
```
```
db()->insert("users"
    ,['name','email','password']
    ,['kevin','email@email.com','9999999']);
```
```
    db()->update("users"
    ,['name'=>'Captain-Crunch','email'=>'mail@mail.com'] // set
    ,['id'=>6]); // where
```
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
echo url("/posts/{$post->id}");
```
#### URLs For Named Routes 
```
[
        ['GET'],
        '/hello/{id}/foo/{sid}',
        [new App\Foo, 'bar'],////object, method
        'nameroute1'//Named Route

],

echo   route('nameroute1', ['id' => 77, 'sid' => 8888]);

// http://example.com/hello/77/foo/8888
``` 