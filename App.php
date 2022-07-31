<?php

include 'vendor/autoload.php';
$dotenv = new DotEnv();

$REQUEST= empty($_REQUEST) ?    file_get_contents("php://input"): $_REQUEST;



try {
    $router = new Router();

    foreach (config('route') as $key => $value) {
        $router->addRoute($value[0][0], $value[1], $value[2]);
    }



    $target = $router->doRouting();

    // var_dump($target);
} catch (Throwable $e) {
    http_response_code(404);
}



// $mysql=new Mysql();

//  $mysql->query("select * from users");
//  $mysql->query("insert into users (name,email,password) values ('test334','s4s@ss.com','123')");
// $result=$mysql->fetchAll();

// var_dump($REQUEST);


// var_dump(config('app.locale'));
