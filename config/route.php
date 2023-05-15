<?php
return [


    [
        ['GET'],
        '/',
        [new App\Index, 'index'],
    ],

    [
        ['GET'],
        '/nav[{navid}].html',
        [new App\Index, 'nav'],
    ],

    [
        ['GET'],
        '/search',
        [new App\Index, 'search'],
    ],

    [
        ['GET'],
        '/tool-{id}[/]',
        [new App\Index, 'tool'],
    ],

    [
        ['GET'],
        '/tool-{id}',
        [new App\Index, 'tool'],
    ],



    [
        ['GET'],
        '/navshow/{navid}/{id}/{id2}/{id3}',
        [new App\Index, 'navshow'],
    ],



    [
        ['GET'],
        '/category-{cid}.html',
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
        '/hello3/{id}/sss/{sid}',
        [new App\Foo, 'bar'],////object, method
        'hahahayyy'//命名路由

    ],

    [
        ['GET'],
        '/a/{uid}/b/{pid}',
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
        '/users/{uid}/posts/[{pid}]',
        function ($uid, $pid = 99) {
            var_dump($uid, $pid);
        },
    ],

 
    ];
