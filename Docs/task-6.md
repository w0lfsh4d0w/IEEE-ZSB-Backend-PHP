## OOP 
in our code we use quires in every file we need it and this is an mess
this is a repeat and this will lead to 
- little safe because we pass the variable in the query 
- hard in Debbugging 
- hard in maintain 

we will make a class for the DataBase  this class will be the only respoinsible for connect and read from Data Base  

```php
<?php

class Database {

    
    protected function connect() {
   
    }

    protected function db_read($query) {
        
        $connection = $this->connect(); 
        
        
    }
}
```
##  Error Message 

when the user try to login you must make the error message if the email or password wrong 
the same like that "Wrong email or password " 


## Access Control (Privileges) 
in our code any logged in user can access any page and this is a mess
this is a huge security risk and this will lead to 
- no difference between Admin and normal User
- hard to manage permissions 
- if an editor account gets hacked, the whole system is in danger

we will add a 'rank' column in the DataBase and make an `access()` function  this function will be the only responsible for checking if the user has the right permission to see the page

```php
<?php

function access($needed_rank) {

    // Get user rank from session
    $user_rank = isset($_SESSION['user_rank']) ? $_SESSION['user_rank'] : '';

    switch ($needed_rank) {
        
        case 'admin':
            $allowed = ['admin'];
            break;

        case 'editor':
            $allowed = ['editor', 'admin'];
            break;

        case 'user':
            $allowed = ['user', 'editor', 'admin'];
            break;

        default:
            return false;
    }

    if (in_array($user_rank, $allowed)) {
        return true; 
    }

    return false; 
}
```

## Sql injection 