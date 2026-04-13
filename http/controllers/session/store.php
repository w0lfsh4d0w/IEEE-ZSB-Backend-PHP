<?php


use core\Authenticator;
use core\Session;
use http\forms\LoginForm;


$email = $_POST['email'];
$password = $_POST['password'];
$form = new LoginForm();


if ($form->validate($email, $password)) {
    $auth = new Authenticator();
    if ($auth->attempt($email, $password)) {
        redirect('/');
    }
    $form->error('email', 'No matching account found for that email address and password.');
}
Session::flash('errors',$form->errors());
return redirect('/login');


// return view('session/create.view.php', [
//     'errors' => $form->errors()
// ]);
