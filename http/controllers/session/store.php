<?php


use core\Authenticator;
use core\Session;
use core\ValidationException;
use http\forms\LoginForm;

try {
    $form = LoginForm::validate($attributes = [
        'email' => $_POST['email'],
        'password' => $_POST['password'],
    ]);
} catch (ValidationException $exception) {
    Session::flash('errors', $form->errors());
    Session::flash('old', [
        'email' => $attributes['email']
    ]);
    return redirect('/login');
}

if ((new Authenticator)->attempt($attributes['email'], $attributes['password'])) {
    redirect('/');
}
$form->error('email', 'No matching account found for that email address and password.');




// return view('session/create.view.php', [
//     'errors' => $form->errors()
// ]);
