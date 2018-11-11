<?php

require __DIR__ . '/vendor/autoload.php';

Flight::route('POST /', function($route){
    $params = $route -> params;
    Flight::json($params);
}, true);

Flight::start();