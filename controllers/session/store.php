<?php

use core\Validator;
use core\App;
use core\Database;

$db = App::resolve(Database::class);
$email = $_POST['email'];
$password = $_POST['password'];
$errors = [];
if (!Validator::email($email)) {
    $errors['email'] = 'Please provide a valid email address.';
}
if (!Validator::string($password)) {
    $errors['password'] = 'Please provide a valid password.';
}

if (!empty($errors)) {
    return view('session/create.view.php', [
        'errors' => $errors
    ]);
}

// mtach the credintals 
$user = $db->query('select * from users where email = :email', [
    'email' => $email
])->find();

// log in it the credintals match 
if ($user) {
    if (password_verify($password, $user['password'])) {
        login([
            'email' => $email
        ]);
        header('location: /');
        exit();
    }
}

return view('session/create.view.php', [
    'errors' => [
        'email' => 'No matching account for that email address and password'
    ]
]);
