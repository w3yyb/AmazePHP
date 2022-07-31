<?php
return [


    [
        ['GET'],
        '/',
        function () {
            echo 'welcome to the homepage';
        },
    ],

    [
        ['GET'],
        '/hello',
        function () {
            echo 'Hello sukei!';
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
        ['POST'],
        '/users',
        function () {
            echo 'post 1111111111';
        },
    ],

    [
        ['GET'],
        '/users/:uid/posts/[:pid]',
        function ($uid, $pid = 99) {
            var_dump($uid, $pid);
            // echo $uid. '~~~~~' .$pid;
        },
    ],

 
    ];
