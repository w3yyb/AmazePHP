<?php
 

return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    BASE_PATH.'/cache/log.log',
                    Monolog\Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\LineFormatter::class,
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],


// log2通道
'log2' => [
    // 处理默认通道的handler，可以设置多个
    'handlers' => [
        [   
            // handler类的名字
            'class' => Monolog\Handler\RotatingFileHandler::class,
            // handler类的构造函数参数
            'constructor' => [
                BASE_PATH.'/cache/log2.log',
                Monolog\Logger::DEBUG,
            ],
            // 格式相关
            'formatter' => [
                // 格式化处理类的名字
                'class' => Monolog\Formatter\LineFormatter::class,
                // 格式化处理类的构造函数参数
                'constructor' => [ null, 'Y-m-d H:i:s', true],
            ],
        ]
    ],
],




];
