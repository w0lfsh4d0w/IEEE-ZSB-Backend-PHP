<?php

namespace core\Middleware;

class Auth
{

    function handle()
    {

        if (!$_SESSION['user'] ?? false) {
            header('location: /');
            exit();
        }
    }
}
