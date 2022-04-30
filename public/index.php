<?php

require_once __DIR__ . './../vendor/autoload.php';

session_start();

$router = new Bramus\Router\Router();

// Dev
$router->get('/', 'Mvc\Controllers\PageController@base');
$router->get('/matches/(\d+-\d+-\d+)', 'Mvc\Controllers\ApiController@matches');
$router->get('/predict/(\d+)', 'Mvc\Controllers\ApiController@predict');

$router->run();