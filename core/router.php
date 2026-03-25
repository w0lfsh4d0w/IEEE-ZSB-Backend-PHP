<?php
$routes=require base_path('routes.php');
function abort($code = 404)
{
    http_response_code($code);
    require base_path("views/response{$code}.php");
    die();
}
function RouteToController($uri, $routes)
{

    if (array_key_exists($uri, $routes)) {
        require base_path($routes[$uri]);
    } else {
        abort(404);
    }
}
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

RouteToController($uri, $routes);
