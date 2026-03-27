<?php

use core\Validator;
use core\App;
use core\Database;

$email = $_POST['email'];
$password = $_POST['password'];
$errors = [];
if (!Validator::email($email)) {
    $errors['email'] = 'Please provide a valid email address.';
}
if (!Validator::string($password, 7, 255)) {
    $errors['password'] = 'Please provide a password at least Seven characters.';
}

if (!empty($errors)) {


    return view('registration/create.view.php', [
        'errors' => $errors
    ]);
}

$db = App::resolve(Database::class);
$user = $db->query('select * from users where email = :email', [
    'email' => $email
])->find();

if ($user) {
    // some one with that email already exist 
    // redirect to login page 
    header('location: /');
    exit();
} else {
    $db->query('insert into users (email,password)
        values(:email , :password)', [
        'email' => $email,
        'password' => $password
    ]);

    // mark that the user logged in 
    $_SESSION['user'] = [
        'email' => $email
    ];
    header('location: /');
    exit();
}
