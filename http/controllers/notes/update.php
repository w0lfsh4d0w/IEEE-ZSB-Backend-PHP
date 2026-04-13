<?php

use core\Validator;
use core\App;
use core\Database;

$db = App::resolve(Database::class);

$currentUserId = 1;

// find the corresponding note 
$note = $db->query('select * from notes where id = :id', [
    'id' => $_POST['id']
])->findOrFail();

// authorize 
Authorize($note['user_id'] === $currentUserId);
// validate
$errors = [];
if (! Validator::string($_POST['body'], 1, 1000)) {
    $errors['body'] = 'A body of no more than 1,000 characters is required.';
}
if (count($errors)) {
    return view('notes/edit.view.php', [
        'heading' => 'Edit Note',
        'errors' => $errors,
        'note' => $note

    ]);
}

$db->query('update notes set body=:body where id=:id',[

'id'=> $_POST['id'],
'body' => $_POST['body']
]);

header('location: /notes');
die();
