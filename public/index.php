<?php
const BASE_BATH = __DIR__.'/../';
require BASE_BATH.'functions.php';
spl_autoload_register(function ($class)
{
    require base_path("core/" .$class. '.php');

} );
require base_path('router.php');

