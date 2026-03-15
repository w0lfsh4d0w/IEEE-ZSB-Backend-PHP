<?php
require 'functions.php';
require 'Database.php';
// require 'router.php';
$dsn = "mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";
$pdo = new PDO($dsn, 'lara_user', 'password123');
$statement = $pdo->prepare('select * from users');
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);


$db=new Database();
$post=$db->query('select * from users')->fetch(PDO::FETCH_ASSOC);

