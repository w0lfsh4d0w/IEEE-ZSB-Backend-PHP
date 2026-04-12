<?php

namespace core;

use core\App;
use core\Database;

class Authenticator
{
    public function attempt($email, $password)
    {
        // mtach the credintals 
        $user = App::resolve(Database::class)->query('select * from users where email = :email', [
            'email' => $email
        ])->find();

        // // log in it the credintals match 
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $this->login([
                    'email' => $email
                ]);
                return true;
            }
        }
        return false;
    }
    public function login($user)
    {
        $_SESSION['user'] = [
            'email' => $user['email']
        ];

        session_regenerate_id(true);
    }
    public function logout()
    {
        Session::destroy();
    }
}
