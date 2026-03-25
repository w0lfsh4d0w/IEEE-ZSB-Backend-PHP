<?php
namespace core ;
use PDO;
class Database
{
    
public $connection;
public $statement;

    function __construct($config,$username='lara_user',$password='password123')
    {
       
        $dsn='mysql:'. http_build_query($config,'',';');
      $this->connection= new PDO($dsn, $username, $password,[
        PDO::ATTR_DEFAULT_FETCH_MODE=> PDO::FETCH_ASSOC
      ]);
       
    }
    function query($query,$params=[])
    {
        
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        return  $this;
    }
    public function get()  {
      return $this->statement->fetchAll();
    }
    function find()
    {
      return $this->statement->fetch();

    }
    function findOrFail()
    {
      $result=$this->find();
      if(!$result)
        {
          abort();
        }
        return $result;
    }

}
