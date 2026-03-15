# Fundamentals

okay i will take the first part and call it **fundamentals**

it include  
(varables, if, array, associative array, and lampda fun, sperate logic)

the first 3 topics is very easy and we know it

let's focus in the others

---

## associative array

when we store complex data in arrays it is hard to depend on index to call it

we need the **key**  
the key point to the value

and this is the **associative array**

```php
<?php
$Books = [
  [
    // 
    // 'key' => 'value' , This is the genirc method 
    "name" => "full of emptiness",
    "author" => "Dr.Emad R. Osman",
    "read_URL" => "http://example.com"
  ],
  [
    "name" => "The Hobbit",
    "author" => "Dr.Matek G. Well",
    "read_URL" => "http://example.com"
  ]
];
?>
```

This is example of associative array

and we need to call it we call it like this

```php
<?php foreach($Books as $book): ?>
  <li>
    <a href="<?= $book['read_URL']; ?>">
      <?= $book['name']; ?> - <i><?= $book['author']; ?></i>
    </a>
  </li>
<?php endforeach; ?>
```
___
## lambda fun

it is **Anonymous Function**  
it is function without name  
it is flexible

why I said flexible?  
because we can store it in variable or push it as an argument to another function

This is simple example

```php
$sayHello = function($name) {
    return "Hello " . $name;
}; // we put semicolon here

echo $sayHello("Ahmed");
```

the power of it appears with arrays and processes that need callback

like this

```php
$ages = [15, 20, 12, 25, 30];

$adults = array_filter($ages, function($age) {
    return $age >= 18;
});

print_r($adults);
// it will return [20, 25, 30]
```
___
## separate logic from the view

when we put the php code (data, filters, function) with html code it is a mess  
and it will be very hard to read and maintain the code

and our goal is to rearrange that mess by separating

The solution is **Separation of Concerns**

the meaning of that is to separate the logic (php) away from the presentation (html)

and we will have two files like:

- `index.php` (logic)
- `index.view.php` (presentation)

the important thing is: who is calling whom

the answer is the logic will call the view

and how they see each other

we put in the logic code a call or a copy from the view code by calling it like this

```php
require 'index.view.php';
```
___

## Build a structure of our project

we add pages to our project like (Home, About, Contact)

to access these pages we have to add links to them in the **nav bar** like this:

```html
<a href="/contact.php">Contact</a>
```

when you request `contact.php` the server searches for it  
and if it finds this URL it brings it and gives it to you

---

### partials

after adding pages we have duplicates because the pages have a lot of the same things

like **header** and **footer**

and if we copy them for each page it becomes **hard-coded**

to avoid that we make **partials** that are common between the pages

for example we can create:

- `banner`
- `footer`
- `head`
- `nav`

and call them from each **view page**
I know this part I didn't explain clear it almost about the view and you must view the pages to be understand but i focus to explain the concept 

___
## Super Globals

How does PHP know the URL of the page?

we need to know the URL of the page, and this leads us to:

`$_SERVER[]`

it is a **super global variable**  
you can use it anywhere, and this is the meaning of **global**

and it does many jobs, but now we will use it to know the URL

`$_SERVER['REQUEST_URI']`

it returns a string like this:

```
string(1) "/"
```

and if we need to know the URL for any page  
we can use a function like this:

```php
function isUrl($value)
{
    return $_SERVER['REQUEST_URI'] === $value;
}
```

and we will use it to make conditions to view specific things  
according to the returned URL from the function
___
## Router

Before we explain what a **router** is and why we need it, let's see our current situation.

When a user visits a page like `example.com/about.php`, the code of `about.php` runs directly.

The meaning of that is:

- Each page is separate and cannot see the other.
- Any function I want to use in another page, I must copy it to that page, which leads to duplication.
- Also, URLs like `about.php` are not professional.

The solution is to create a **single entry point**, like:

- `router.php`
- or `index.php`

```php
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

if ($uri === '/') {
    require 'controllers/index.php';
} elseif ($uri === '/about') {
    require 'controllers/about.php';
} elseif ($uri === '/contact') {
    require 'controllers/contact.php';
}
```

This is just initial code to establish the idea.

The first step is to take the URL from the browser  
using `$_SERVER['REQUEST_URI']` and validate it using `parse_url`  
to check if it has a string after `?`.

After that, we compare the URL with our defined URLs.  
If it matches, we will render the page.

This is just an initial example to make the idea clear.
___
## PDO First Steps

In any application we have data, and we need to process this data and view it.

PHP provides **PDO (PHP Data Objects)** which offers a safe method to connect to our database.

It needs some information to connect, like:

- the type of database server  
- the host  
- the port  
- user information  

and we do it in PHP like this:

```php
$dsn = "mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";
$pdo = new PDO($dsn, 'lara_user', 'password123');
```

now we have an instance of the **PDO class** connected to our database.

---

The second step is to prepare the query we want to run.

```php
$stmt = $pdo->prepare('SELECT * FROM posts');
```

why **prepare**?

it sends the query to the database to prepare it and check the syntax.

it returns an object from **PDOStatement**.

by this we separate between the query and the data.

after that we execute the query:

```php
$stmt->execute();
```

and fetch or receive the data like this:

```php
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

it returns the data as an **associative array**.

___
# Database Class

after that step, when we made the connection and fetched data from the database, we want to arrange that mess.

we will put it in a class called **Database Class**.

there are some steps that we do every time we want to execute a query, like the **PDO connection**.  
that is not logic related to the query itself, so we separate it in the **constructor**.

the constructor will be called automatically when we create an instance from the Database class.

and we rearrange it like this:

```php
class Database
{
    public $connection;

    function __construct()
    {
        $dsn = "mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";
        $this->connection = new PDO($dsn, 'lara_user', 'password123');
    }

    function query($query)
    {
        $statement = $this->connection->prepare($query);
        $statement->execute();

        return $statement;
    }
}
```

and i will take the return from the statement and display it as i want.
__

## Make Database Class Clean and Flexible

**clean** → easy to read and understand  
**flexible** → easy to edit and add features without breaking other functions

our problem is:

```php
$dsn = "mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";
$pdo = new PDO($dsn, 'lara_user', 'password123');
```

This is **hard-coded**.

Imagine now we are in development, staging, or any other environment and we want to change this configuration.  
Now it is hard because it is hard-coded.

The solution is to **separate the configuration away from the Database class**.

```php
config.php
<?php

// This file returns our configuration
return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'myapp',
    'charset' => 'utf8mb4'
];
```

When we want to use it, we require it like this:

```php
$config = require 'config.php';

$db = new Database($config['database']);
```

and we can have different config files that contain the configuration settings  
for different environments like **development** or **production**,  
and we choose the correct one.

---

now we want to make the **DSN dynamic**.

we will use the function `http_build_query()`.

```php
function __construct($config, $username, $password)
{
    $dsn = 'mysql:' . http_build_query($config, '', ';');
}
```

this makes the query dynamic, and the username and password are also passed dynamically.

---

and if we want to add options like the **retrieve data option**,  
we can put them in an **options array** for PDO.

like this:

```php
new PDO($dsn, $username, $password, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
```