<?php
$heading = "Note";
$config = require('config.php');
$db = new Database($config['database']);
$note = $db->query(
  'select * from notes where id = :id  ',
  ['id' => $_GET['id'],]
)->fetch();
if (!$note) {
  abort();
}
$currentUserID = 1;
if ($note['user_id'] !== $currentUserID) {
  abort(Response::FORBIDDEN);
}
require "views/note.view.php";
