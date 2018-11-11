<?php

require_once '/vendor/autoload.php';

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();