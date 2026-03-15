<?php
require 'functions.php';
require 'Database.php';
//require 'router.php';
$config=require 'config.php';
$db=new Database($config['database']);
$post=$db->query('select * from users')->fetchAll();
dd($post);

