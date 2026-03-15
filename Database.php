<?php
class Database
{
    
public $connection;
    function __construct()
    {
        $dsn = "mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";
      $this->connection= new PDO($dsn, 'lara_user', 'password123');
       
    }
    function query($query)
    {
        
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return  $statement;
    }
}