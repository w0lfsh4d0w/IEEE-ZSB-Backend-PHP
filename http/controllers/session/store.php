<?php

use core\App;
use core\Database;
use http\forms\LoginForm;

$db = App::resolve(Database::class);
$email = $_POST['email'];
$password = $_POST['password'];
$form = new LoginForm();


if (!$form->validate($email, $password)) {
    return view('session/create.view.php',[
        'errors' => $form->errors()
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
