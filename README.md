# AmazePHP
## AmazePHP  
A good choice for starting a PHP small project, especially for APIs.   
It only takes 1 minute to start a project.


## features:  
- configuration  
- .env  
- router    
- mysql class  
- http client  
- log   
- template engine
- error handling  
- Singleton 
- macroable 
- cache 


## install:    
git clone https://github.com/w3yyb/AmazePHP.git  
composer install  

 ## run
 php -S 0.0.0.0:9080  public/index.php  
 open http://localhost:9080

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

 ## usage 
 ### get config  
 ``` 
  config('app'); //will read config/app.php  app.php is return an array.  
  config('app.url')// == config('app')['url'];  
  ``` 
  
 ### set config 
 ``` 
 config(['sample.haha' =>'6666']);
``` 
``` 
 config(['sample.hahahaha' =>[
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
 
### get cache 
```
value = cache('key');
```
### set cache 
```
cache(['key' => 'value'], 10);
``` 
### view
```
echo view('greeting', ['name' => 'James']);
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
        '/users/:uid/posts/[:pid]',
        function ($uid, $pid = 99) {
            var_dump($uid, $pid);
        },
    ],
```
```
       [
        ['GET'],
        '/a/:uid/b/:pid',
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
        '/tool-:id[/]',//Remove the trailing slash
        [new App\Index, 'tool'],
    ],
```
    
    
