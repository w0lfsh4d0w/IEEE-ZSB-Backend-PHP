<?php

use core\Session;
use core\ValidationException;
const BASE_BATH = __DIR__ . '/../';
require BASE_BATH . 'vendor/autoload.php';
session_start();

require BASE_BATH . 'core/functions.php';

// spl_autoload_register(function ($class) {
//     $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
//     require base_path("{$class}.php");
// });
require base_path('bootstrap.php');
$router = new \core\Router();


$routes = require base_path('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
try {
$router->route($uri, $method);
}
catch(ValidationException $exception)
{
    Session::flash('errors', $exception->errors);
    Session::flash('old',$exception->old);
    return redirect($router->previosUrl());
}

Session::unflash();