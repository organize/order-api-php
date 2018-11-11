<?php

require __DIR__ . '/vendor/autoload.php';

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();