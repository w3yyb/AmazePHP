<?php
return [


    [
        ['GET'],
        '/',
        [new App\Index, 'index'],
    ],
    [
        ['GET'],
        '/search',
        [new App\Index, 'search'],
    ],

    [
        ['GET'],
        '/tool-:id[/]',
        [new App\Index, 'tool'],
    ],

    [
        ['GET'],
        '/tool-:id',
        [new App\Index, 'tool'],
    ],



    [
        ['GET'],
        '/category-:cid.html',
        [new App\Index, 'category'],

    ],


    [
        ['PUT,GET'],
        '/hello',
        function () {
            echo 'Hello AmazePHP!';
        },
    ],

    [
        ['GET'],
        '/hello2',
        'callbackFunction',
    ],

    [
        ['GET'],
        '/hello44.php',
        function () {
            include 'App/aaaaa.php';
        },
    ],

    [
        ['GET'],
        '/hello3/:id',
        [new App\Foo, 'bar'],////object, method
    ],

    [
        ['GET'],
        '/a/:uid/b/:pid',
        ['App\myclass', 'say_hello'],//static method
    ],

    [
        ['GET,POST'],
        '/users',
        function () {
            echo 'post AmazePHP';
        },
    ],

    [
        ['*'],
        '/users/:uid/posts/[:pid]',
        function ($uid, $pid = 99) {
            var_dump($uid, $pid);
        },
    ],

 
    ];
