<?php


use core\Authenticator;

use http\forms\LoginForm;


$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password'],
]);


if ((new Authenticator)->attempt($attributes['email'], $attributes['password'])) {
    redirect('/');
}
$form->error('email', 'No matching account found for that email address and password.');




// return view('session/create.view.php', [
//     'errors' => $form->errors()
// ]);
