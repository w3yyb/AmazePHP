<div align="center">

中文 | [English](./README.md)

</div>  

# AmazePHP
## 关于 AmazePHP  
AmazePHP 是启动 PHP 项目的绝佳选择，适用于 Web 和 API 开发。只需一分钟即可启动项目。它具有超高性能和非常易于使用的开发体验。
没有复杂的概念，因此学习曲线最低。

## 特性：  
- 配置  
- 环境变量  
- 路由  
- 请求  
- 响应  
- 控制器  
- 中间件  
- 门面（Facades）  
- 容器  
- 数据库  
- HTTP 客户端  
- 日志  
- 视图与模板  
- 错误处理  
- 缓存  
- 会话  
- Cookie  
- URL 生成  
- CSRF 保护  

## 安装：    
```
composer create-project amazephp/amazephp
```
或使用 git clone：
```
git clone https://github.com/w3yyb/AmazePHP.git  

cd AmazePHP 

composer install  
```
## 运行
```
cd public/  

php -S localhost:9080 server.php  
```
在浏览器中打开 http://localhost:9080。

## 要求：  
PHP 8.1+  

## 目录结构  

### App 目录  
app 目录包含应用程序的核心代码。我们稍后将详细探讨此目录；然而，应用程序中几乎所有的类都将位于此目录中。  
### config 目录  
config 目录，顾名思义，包含应用程序的所有配置文件。包括路由配置文件。
### helper 目录  
包含辅助函数。
### AmazePHP 目录
框架核心目录，包含一些库类。您也可以将自己的类文件放在此目录中。
### public 目录
public 目录包含 index.php 文件，它是所有请求进入应用程序的入口点，并配置自动加载。此目录还包含您的资源文件，如图片、JavaScript 和 CSS。
### cache 目录
cache 目录包含缓存文件，包括日志文件。
### template 目录
template 目录包含您的 HTML 模板文件。

# 使用 
## 配置 
### 获取配置  
``` 
config('app'); //将读取 config/app.php，app.php 返回一个数组。  

config('app.url')// == config('app')['url'];  

// 如果配置值不存在，则检索默认值...
$value = config('app.timezone', 'Asia/Seoul');
``` 
  
### 设置配置 
要在运行时设置配置值，请将数组传递给 config 函数：
``` 
config(['app.timezone' => 'America/Chicago']);
``` 
 
 
## 缓存  
### 获取缓存 
```
$value = cache('key');
```
### 设置缓存 
```
cache(['key' => 'value'], 10);// 10 秒后过期
``` 
### 视图 
模板引擎使用 `BladeOne`，这是一个类似于 Laravel `blade` 的模板引擎，点击此处 https://github.com/EFTEC/BladeOne/wiki/BladeOne-Manual 查看 BladeOne 手册。 
```
echo view('greeting', ['name' => 'James']);
``` 
第一个参数是模板名称，即 `template/greeting.blade.php`，第二个参数是传递给模板的变量。 
### 获取环境配置 
```
env('key');

env('key','default'); 
```
传递给 env 函数的第二个值是“默认值”。如果给定键的环境变量不存在，则将返回此值。

### 日志
```
logger('some msg');//错误日志
```
```
logger('some msg','warning'); //警告日志 | 支持：emergency, alert, critical, error, warning, notice, info, debug 
```
### 路由 
参见 config/route.php
```
  [
        ['GET'], 
        '/',  
        [App\Controllers\Index::class, 'index'],
        'routename',
        'middleware'=>[App\Middleware\a2Middleware::class,App\Middleware\b2Middleware::class],
  ],
```
第一行是 HTTP 请求方法，支持 HEAD, GET, POST, PUT, PATCH, DELETE。`['POST,GET']` 表示同时支持 POST 和 GET。`['*']` 表示支持所有 HTTP 方法。  

第二行表示路径，如 `/users/{uid}/posts/[{pid}][/]`：花括号中的是变量参数，方括号中的是可选参数，即 URL 中未传递的参数，`[/]` 用于移除尾部斜杠。  

第三行表示 PHP 回调，支持类方法、类的静态方法、匿名函数、函数等。 

第四行是可选的，表示命名路由的名称。 

middleware 键是可选的，用于注册路由中间件。

### 请求 
AmazePHP\Request 类提供了一种面向对象的方式来与应用程序处理的当前 HTTP 请求进行交互，并检索与请求一起提交的输入、Cookie 和文件。


#### 用法
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
更多用法参见 AmazePHP/src/Request.php。

### 响应  
AmazePHP 出于性能原因没有提供 Respose 类。 
在控制器或中间件中使用 header() 函数和 echo 或 return 来响应。
### 控制器 
与其将所有请求处理逻辑定义为路由文件中的闭包，您可能希望使用“控制器”类来组织此行为。控制器可以将相关的请求处理逻辑分组到一个类中。例如，`UserController` 类可能处理与用户相关的所有传入请求，包括显示、创建、更新和删除用户。默认情况下，控制器存储在 `app/Controllers` 目录中。
#### 编写控制器 
在 `app/Controllers` 目录中，您可以编写一些控制器，例如：
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
编写控制器类和方法后，您可以像这样定义到控制器方法的路由：
```
 [
        ['GET'],
        '/',
        [App\Controllers\Index::class, 'index']
 ],
```
当传入请求与指定的路由 URI 匹配时，将调用 `App\Controllers\Index` 类上的 index 方法，并将路由参数传递给该方法。 
### 中间件 
中间件，也称为 HTTP 中间件，主要用于修改或过滤 HTTP 请求或响应。所有这些中间件都位于 `app/Middleware` 目录中。  
 
中间件分为前置中间件和后置中间件。前置中间件主要用于修改 HTTP 请求。后置中间件主要用于修改 HTTP 响应。

```
请求->前置中间件->实际操作->后置中间件->响应
```
#### 定义中间件 
前置中间件和后置中间件的主要区别在于代码执行的位置。 
在 `app/Middleware` 目录中：
##### 定义前置中间件 
创建例如 bMiddleware.php
```
<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;

class bMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {
        
        //在此处执行一些逻辑

        return $next($object);
    }

}
?>

```
##### 定义后置中间件 
创建例如 aMiddleware.php

```
<?php
namespace App\Middleware;
use AmazePHP\MiddlewareInterface;

class aMiddleware implements MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {

        $response = $next($object);
         //在此处执行一些逻辑

        return $response;
    }

}
?>

```
#### 注册全局中间件
在 config/middleware.php 中编写以下内容：
```
return [
    App\Middleware\aMiddleware::class,
    App\Middleware\bMiddleware::class,
];
```
#### 注册路由中间件 
参见路由部分。

### 门面（Facades）  
门面为框架核心类库的（动态）类提供了静态调用接口。使您能够静态调用动态类方法。
#### 示例：
在 `App\Controllers\Index` 控制器中：

```
<?php
namespace App\Controllers;
use AmazePHP\Facade\Request;
class Index  
{
    public function index()
    {
        echo Request::url();//静态调用 Request.php 的 url 方法。与调用 Request->url() 相同。
    }
}
```
系统的所有门面都放置在 `AmazePHP/src/Facade` 目录中。

### 容器  
容器是管理类依赖项和执行依赖注入的强大工具。

#### 用法：
例如：
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
bar 方法依赖于 Request 类。您可以将 Request $request 放在 bar 方法的参数中。框架将自动调用 Request 类，因此您可以使用 Request 类的方法，例如：`$request->url()`。  
支持使用依赖注入的场景包括（但不限于）： 
- 控制器方法；
- 路由的闭包定义；
- 中间件的执行方法；
    
### HTTP 客户端 
```
$response= httpGet('http://httpbin.org/get');
$response= httpGet('http://httpbin.org/get',['headername'=>'headervalue']);
$response= httpHead('http://httpbin.org/get',['headername'=>'headervalue']);
$response= httpDelete('http://httpbin.org/delete',['headername'=>'headervalue']);
$response= httpPost('http://httpbin.org/post',['senddataname'=>'senddatavalue']);
$response= httpPut('http://httpbin.org/put',['senddataname'=>'senddatavalue']);
$response= httpPatch('http://httpbin.org/patch',['senddataname'=>'senddatavalue']);
```
$response 是一个包含 status_code、header 和 body 数据的数组。  

函数参数如下： 
```
httpGet($url,$header = [])
httpHead($url,$header = [])
httpDelete($url,$header = [])

httpPost($url, $data, $isJson = true,$method='POST',$header = [])
httpPut($url, $data, $isJson = true,$method='PUT',$header = [])
httpPatch($url, $data, $isJson = true,$method='PATCH',$header = [])
```
## 会话 
会话默认关闭，如果您想开启，请将 .env 文件中的 SESSION_ENABLE 更改为 true。
### 设置会话
```
session(["name" => "value"]);
```        
### 获取会话
```
$value = session('name')
```
## Cookie 
### 获取 Cookie
```
$value = request()->cookie('name');
```
### 设置 Cookie 
```
cookie('name','value',86400); // 86400 秒
```

### 数据库
数据库组件使用 `PdoOne`，这是一个用于 PHP 和 PDO 的数据库访问对象包装器。点击 https://github.com/EFTEC/PdoOne 查看如何使用它。 

以下是一个简单的使用示例。  

查询： 
```
$results = db()->select("*")->from('users')->where("name like '%test%'")->toList();

print_r($results);
``` 
使用原始 SQL： 
```
$sql='select * from users where id=1';

$pdoStatement=db()->runRawQuery($sql,[],false);  // [] 是参数

print_r($pdoStatement->fetchAll());
```
插入： 
```
db()->insert("users"
    ,['name','email','password']
    ,['kevin','email@email.com','123456']);
```
更新： 
```
db()->update("users"
,['name'=>'Captain-Crunch','email'=>'mail@mail.com'] // 设置
,['id'=>6]); // 条件
```
删除： 
```
db()->delete("users"
  ,['id'=>6]); // 条件
```
    
### URL
#### 访问当前 URL 
```
// 获取不带查询字符串的当前 URL...
echo url()->current();
 
// 获取包含查询字符串的当前 URL...
echo url()->full();
 
// 获取上一个请求的完整 URL...
echo url()->previous(); 
```
#### 生成 URL
```
echo url("/posts/{$post->id}"); // http://example.com/posts/1
```
#### 命名路由的 URL 
```
[
        ['GET'],
        '/hello/{id}/foo/{sid}',
        [new App\Foo, 'bar'],
        'nameroute1'//命名路由
],

echo   route('nameroute1', ['id' => 1, 'sid' => 2]);

// http://example.com/hello/1/foo/2
``` 
## CSRF 保护 
如果您想使用它，请首先启用会话。 

跨站请求伪造（CSRF）是一种恶意攻击，攻击者在未经授权的情况下以认证用户的名义执行命令。幸运的是，AmazePHP 使得保护您的应用程序免受跨站请求伪造（CSRF）攻击变得容易。

AmazePHP 自动为应用程序管理的每个活动用户会话生成一个 CSRF“令牌”。此令牌用于验证认证用户是否确实是向应用程序发出请求的人。由于此令牌存储在用户的会话中，并且每次会话重新生成时都会更改，因此恶意应用程序无法访问它。 

当前会话的 CSRF 令牌可以通过 `csrf_token` 辅助函数访问：
```
$token = csrf_token();
```
每当您在应用程序中定义“POST”、“PUT”、“PATCH”或“DELETE” HTML 表单时，您都应在表单中包含一个隐藏的 CSRF `_token` 字段，以便 CSRF 保护中间件可以验证请求。为了方便起见，您可以使用 `@csrf` Blade 指令生成隐藏的令牌输入字段：
```
<form method="POST" action="/profile">
    @csrf
 
    <!-- 等同于... -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
</form>
```
### X-CSRF-TOKEN 
除了检查 CSRF 令牌作为 POST 参数外，`lib\VerifyCsrfToken` 还将检查 `X-CSRF-TOKEN` 请求头。例如，您可以将令牌存储在 HTML `meta` 标签中：
```
<meta name="csrf-token" content="{{ csrf_token() }}">
```
然后，您可以指示像 jQuery 这样的库自动将令牌添加到所有请求头中。这为使用传统 JavaScript 技术的基于 AJAX 的应用程序提供了简单、方便的 CSRF 保护：
```
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```
### X-XSRF-TOKEN 
AmazePHP 将当前 CSRF 令牌存储在加密的 `XSRF-TOKEN` Cookie 中，该 Cookie 包含在框架生成的每个响应中。您可以使用 Cookie 值来设置 `X-XSRF-TOKEN` 请求头。

此 Cookie 主要是为了方便开发者，因为一些 JavaScript 框架和库（如 Angular 和 Axios）会自动将其值放在同源请求的 `X-XSRF-TOKEN` 头中。
## 测试
```
./phpunit --bootstrap vendor/autoload.php tests
./phpunit --bootstrap vendor/autoload.php tests  --display-warnings
./phpunit --bootstrap vendor/autoload.php tests  --display-deprecations
```

## 基准测试 
AmazePHP 在 `hello world` 基准测试中比 Laravel 快约 9 倍。  
Laravel：2900 rps。  
AmazePHP：23000 rps。  
两者都开启了调试，并且 Laravel 使用了 Array Session Driver。
