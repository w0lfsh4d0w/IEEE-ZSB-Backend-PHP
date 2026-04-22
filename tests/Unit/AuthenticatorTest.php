<?php

use core\Authenticator;

it('sure the user is logged in ', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $auth = new Authenticator();
    $user = ['email' => 'ahmed@example.com'];
    $auth->login($user);
    expect($_SESSION['user']['email'])->toBe('ahmed@example.com');
});
