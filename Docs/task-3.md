## Resourceful Naming Conventions

now we want to split our files depending on the resource.

for example, notes:  
we have many files related to notes,  
so we create a folder called `notes` and put all note controllers inside it.  

we do the same for note view files.

---

after that, we follow **standard naming conventions**.

we use a consistent naming style for every resource:

- `index.php` → list all items  
- `create.php` → create a new note  
- `show.php` → show a single note  

---

after organizing files, we update the route paths.

```php
return [
	'/' => 'controllers/index.php',
	'/about' => 'controllers/about.php',

	'/notes' => 'controllers/notes/index.php',
	'/note' => 'controllers/notes/show.php',
	'/notes/create' => 'controllers/notes/create.php',
];
```

___
## PHP Autoloading and Extractions

now we want to refactor our project to make it more secure, flexible, and scalable.

---

### Problem

at the beginning, the document root contains all files like:

- config.php  
- router.php  

look!!

if the user accesses a file directly from the browser like:

```
localhost:8888/config.php
```

he will see sensitive data, and this is a disaster.

we don’t want the user to access internal files directly.  
we want him to always pass through a single entry point → `index.php`.

---

### Solution → Public Folder

we create a folder called `public`  
and move:

- `index.php`  
- CSS / JS files  

inside it.

then we make this folder the document root:

```bash
php -S localhost:8888 -t public
```

now the application has a single entry point.

---

### Fixing Broken Paths

after this change, all require paths will break.

so we define a base path for the project:

```php
const BASE_PATH = __DIR__ . '/../';
```

then create a helper function:

```php
function base_path($path) {
	return BASE_PATH . $path;
}

require base_path('core/functions.php');
require base_path('Database.php');
```

this function builds the full path based on the project root.

---

### Creating a View Helper

instead of writing full paths every time,  
we create a helper function:

```php
function view($path) {
	require base_path('views/' . $path);
}

view('index.view.php');
```

---

### Passing Data to Views

now we have a problem:

variables defined in the controller are not available in the view.

solution → pass data as an associative array and use `extract()`:

```php
$data = [
	'name' => 'Mohamed',
	'age' => 20
];

extract($data);

echo $name;
echo $age;
```

---

### Autoloading Classes

problem:

every time we use a class (Database, Validator),  
we must require it manually in `index.php`.

this becomes messy with many classes.

---

### Solution → spl_autoload_register

```php
spl_autoload_register(function ($class) {
	require base_path("{$class}.php");
});
```

now when we create a class:

```php
$db = new Database();
```

PHP will automatically search and load the file.

---

## Final Result

- secure structure (no direct access to sensitive files)  
- clean entry point (`index.php`)  
- reusable helpers  
- automatic class loading  
___

## Namespaces

what is it?

imagine you have two files in your project with the same class name.  
how do you differentiate between them?

namespaces are like logical containers that group classes and avoid naming conflicts.

### How to Define a Namespace

we define it inside the class file:

```php
<?php

namespace Core;

class Database {
	// class methods
}
```

---

### Problem After Adding Namespace

now the project will break. why?

when you call:

```php
$db = new Database();
```

PHP will look for:

```
Core\Database
```

not just `Database`.

---

### Solution → Import the Class

we use the `use` keyword in the controller:

```php
use Core\Database;

$db = new Database();
```

---

### Fixing Autoloading with Namespaces

before, our autoloader worked like this:

```
Core/Database.php
```

but now PHP gives:

```
Core\Database
```

with backslashes.

the problem:

- namespaces use `\`
- operating systems use `/`

---

### Solution → Replace Slashes

```php
spl_autoload_register(function ($class) {

	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

	require base_path("{$class}.php");
});
```

---

### Problem with Global Classes (PDO)

when we are inside a namespace like `Core`,  
PHP assumes all classes belong to this namespace.

so if we write:

```php
$pdo = new PDO();
```

PHP will search for:

```
Core\PDO
```

and this will cause an error:

```
Class 'Core\PDO' not found
```

---

### Solution 1 → Use Global Namespace

```php
$pdo = new \PDO(...);
```

this tells PHP to look in the global namespace.

---

### Solution 2 → Import the Class

```php
namespace Core;

use PDO;

class Database {
	public function connect() {
		$pdo = new PDO(...);
	}
}
```

---
___

## Add Delete Note

now in a page like `note.php`, we show a single note,  
and we want to add a **Delete button** to delete this note.

we will not use an anchor tag `<a>`  
because it is **idempotent**, and we explained that before.  

so we will use a form to send a **POST request**.

---

### In show.view.php

```html
<form method="POST">
    <input type="hidden" name="id" value="<?= $note['id'] ?>">
    
    <button type="submit" class="text-sm text-red-500 mt-4">
        Delete
    </button>
</form>
```

`<input type="hidden" ...>`  
this input is hidden because it is not useful for the user to see it,  
but it will be sent to the controller in the POST request  
to tell us which note to delete.

---

### Controller Code

```php
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // fetch the note to verify ownership
    $note = $db->query('SELECT * FROM notes WHERE id = :id', [
        'id' => $_POST['id']
    ])->findOrFail();

    $currentUserId = 1;

    // authorization check
    authorize($note['user_id'] === $currentUserId);

    // delete the note
    $db->query('DELETE FROM notes WHERE id = :id', [
        'id' => $_POST['id']
    ]);

    // redirect after delete
    header('Location: /notes');
    exit();

} else {

    // fetch note for display
    $note = $db->query('SELECT * FROM notes WHERE id = :id', [
        'id' => $_GET['id']
    ])->findOrFail();

    authorize($note['user_id'] === 1);

    view('notes/show.view.php', [
        'heading' => 'Note',
        'note' => $note
    ]);
}
```

---

### What This Code Does

1. detect the request method (POST or GET)  
2. if POST → fetch the note (not to display it, but to check ownership)  
3. compare the current user id with the note owner (to prevent IDOR)  
4. if authorized → execute delete query  
5. redirect back to the notes page  

___
## Build a Better Router

our router was depending only on the URI.  
for example, if the user visits `/note`, it goes to `note.php`.

the problem:

we have different actions on the same URI:

- show note → GET  
- create note → POST  
- delete note → DELETE  

all of them use `/note`.

this forces us to write complex `if` conditions in the controller  
to check the request method, and this makes the code messy and hard to maintain.

---

### Solution

we create a router object responsible for:

- handling URI  
- handling request method  

we define routes like this:

```php
// routes.php

$router->get('/', 'controllers/index.php');
$router->get('/note', 'controllers/notes/show.php');

$router->post('/note', 'controllers/notes/store.php');

$router->delete('/note', 'controllers/notes/destroy.php');
```

---

### Router Class

```php
<?php

class Router {

    protected $routes = [];

    protected function add($method, $uri, $controller) {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function get($uri, $controller) {
        $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller) {
        $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller) {
        $this->add('PATCH', $uri, $controller);
    }

    public function put($uri, $controller) {
        $this->add('PUT', $uri, $controller);
    }
}
```

---

### Explanation

- `$routes` is protected → cannot be modified directly  
- `add()` → avoids repeating code in every method  

---

### Registering Routes

in `index.php`:

```php
$router = new \Core\Router();

$routes = require base_path('routes.php');
```

this will store all routes inside the `$routes` array.

---

### Route Method

```php
public function route($uri, $method) {
    foreach ($this->routes as $route) {
        if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
            return require base_path($route['controller']);
        }
    }

    $this->abort();
}

protected function abort($code = 404) {
    http_response_code($code);
    require base_path("views/{$code}.php");
    die();
}
```

this function compares:

- incoming URI  
- request method  

with stored routes.

---

### Problem with Forms

HTML forms only support:

- GET  
- POST  

so we cannot send DELETE, PUT, PATCH directly.

---

### Solution → Method Spoofing

```html
<form method="POST" action="/note">
    <input type="hidden" name="_method" value="DELETE">
    
    <button type="submit">Delete Note</button>
</form>
```

---

### Entry Point (index.php)

```php
<?php

require 'Router.php';

$router = new Router();

require 'routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);
```

---
### What Happens in index.php (Step by Step)

1. load the core files and create a Router instance

```php
require 'Router.php';
$router = new Router();
```

---

2. load the routes file to register all routes inside the router

```php
require 'routes.php';
```

---

3. get the current URI from the browser request

```php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
```

---

4. detect the real request method

if there is a hidden `_method` field (method spoofing), use it  
otherwise use the default request method from the server

```php
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
```

---

5. send the URI and method to the router to handle the request

```php
$router->route($uri, $method);
```
___
## One Request, One Controller

`note.php` was responsible for both:

- showing the note  
- deleting the note  

we want to apply **Single Responsibility Principle**  
→ one controller = one job.

---

### Update Routes

```php
$router->get('/note', 'controllers/notes/show.php');

$router->delete('/note', 'controllers/notes/destroy.php');
```

---

### Destroy Controller

in `controllers/notes/destroy.php`  
this file will only handle delete requests.

we don’t need to check the request method again,  
because the router already handles it.

```php
<?php

$config = require base_path('config.php');
$db = new Database($config['database']);

$id = $_POST['id'];

// authorization check
$currentUserId = 1;

$note = $db->query('select * from notes where id = :id', [
    'id' => $id
])->findOrFail();

authorize($note['user_id'] === $currentUserId);

// delete query
$db->query('delete from notes where id = :id', [
    'id' => $id
]);

// redirect after delete
header('location: /notes');
exit();
```

---

## RESTful Conventions

we also split the create logic into two controllers:

- `create` → show the form  
- `store` → handle form submission  

---

### Routes

```php
$router->get('/notes/create', 'controllers/notes/create.php');

$router->post('/notes', 'controllers/notes/store.php');
```

---

### Create Controller

this controller only returns the view (form):

```php
<?php

view("notes/create.view.php", [
    'heading' => 'Create Note',
    'errors' => []
]);
```

---

### Store Controller

this controller handles the POST request:

```php
<?php

$config = require base_path('config.php');
$db = new Database($config['database']);

$errors = [];

// validation
if (! Validator::string($_POST['body'], 1, 1000)) {
    $errors['body'] = 'A body of no more than 1,000 characters is required.';
}

// early return if validation fails
if (! empty($errors)) {
    return view("notes/create.view.php", [
        'heading' => 'Create Note',
        'errors' => $errors
    ]);
}

// insert into database
$db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
    'body' => $_POST['body'],
    'user_id' => 1
]);

// redirect after success
header('location: /notes');
die();
```

---
____
## Make first service container
our proplem is Repeated Data Base setup every time an any file we want to call our db class er repear this
```php
$config = require base_path('config.php');

$db = new Database($config['database']);
````

the soultion is to build the database once of time and oush it in container and wehen we need it we take it from container

```php
<?php

class Container
{
    protected $bindings = [];

    public function bind($key, $resolver)
    {
        $this->bindings[$key] = $resolver;
    }

    public function resolve($key)
    {
        if (! array_key_exists($key, $this->bindings)) {
            throw new Exception("No matching binding found for {$key}");
        }

        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }
}
```

we have tow main funs in our container
bind -> take the key and our fun is depend on build the object
resolve -> find the key in our protected array and run the function

our Bootstrap Playground

to call this functions

```php
<?php

$container = new Container();

$container->bind('Core\\Database', function () {
    $config = require base_path('config.php');
    return new Database($config['database']);
});
```

we want to call this fuction frim any wher we make a app clas and this function be static

```php
<?php

class App
{
    protected static $container;

    public static function setContainer($container)
    {
        static::$container = $container;
    }

    public static function container()
    {
        return static::$container;
    }
}
```

and in the boot strap we call the class

```php
App::setContainer($container);
```

and any where in our controllers we call data bse by this

```php
$db = App::resolve(Database::class);

$db->query("DELETE FROM notes WHERE id = 1");
```
___

````markdown id="f9xk2p"
## Update Note With Patch Request
any system depends on CRUD (Create, Read, Update, Delete) in our app we already have Create, Read, Delete now we add Update
in note view page we add edit button
```php
<footer class="mt-6">
    <a href="/note/edit?id=<?= $note['id'] ?>" class="bg-gray-500 text-white px-4 py-2 rounded">Edit</a>
    <form method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="id" value="<?= $note['id'] ?>">
        <button class="text-sm text-red-500">Delete</button>
    </form>
</footer>
````

update routes

```php
$router->get('/note/edit','controllers/notes/edit.php');
```

edit controller: get note, authorize, go to edit view

```php
<?php
use Core\App;
use Core\Database;

$db=App::resolve(Database::class);
$currentUserId=1;

$note=$db->query('SELECT * FROM notes WHERE id=:id',['id'=>$_GET['id']])->findOrFail();
authorize($note['user_id']===$currentUserId);

require base_path('views/notes/edit.view.php');
```

edit view form (PATCH request)

```php
<form method="POST" action="/notes">
    <input type="hidden" name="_method" value="PATCH">
    <input type="hidden" name="id" value="<?= $note['id'] ?>">
    <div>
        <label for="body">Description</label>
        <textarea id="body" name="body"><?= $note['body'] ?></textarea>
        <?php if(isset($errors['body'])): ?>
            <p class="text-red-500 text-xs mt-2"><?= $errors['body'] ?></p>
        <?php endif; ?>
    </div>
    <div class="mt-6 flex gap-x-4">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        <a href="/notes" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>
```

route for update

```php
$router->patch('/notes','controllers/notes/update.php');
```

update controller: authorize, validate, update, redirect

```php
<?php
use Core\App;
use Core\Database;
use Core\Validator;

$db=App::resolve(Database::class);
$currentUserId=1;

$note=$db->query('SELECT * FROM notes WHERE id=:id',['id'=>$_POST['id']])->findOrFail();
authorize($note['user_id']===$currentUserId);

$errors=[];
if(!Validator::string($_POST['body'],1,1000)){
    $errors['body']='A body of no more than 1,000 characters is required.';
}

if(count($errors)){
    return require base_path('views/notes/edit.view.php');
}

$db->query('UPDATE notes SET body=:body WHERE id=:id',[
    'body'=>$_POST['body'],
    'id'=>$_POST['id']
]);

header('location: /notes');
die();
```
___

## Session And Registration
our http protocol is stateless -> meaning if you change any page the app doesn't know you, session makes it stateful
start session
```php
<?php
// index.php
// this function must be called before any session interaction
session_start();
````

put data

```php
<?php
// IndexController.php
// store name inside a key called 'name'
$_SESSION['name'] = 'Jeffrey';
```

if we make a register page

```php
// routes.php
$router->get('/register','controllers/registration/create.php');
```

```php
// controllers/registration/create.php
// this file only renders the view page
require 'views/registration/create.view.php';
```

form to send data

```php
<form action="/register" method="POST">
    <div>
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>
        <?php if(isset($errors['email'])): ?>
            <p class="text-red-500 text-xs mt-2"><?= $errors['email'] ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <?php if(isset($errors['password'])): ?>
            <p class="text-red-500 text-xs mt-2"><?= $errors['password'] ?></p>
        <?php endif; ?>
    </div>
    <button type="submit">Register</button>
</form>
```

go to controller and validate

```php
// controllers/registration/store.php

$email=$_POST['email'];
$password=$_POST['password'];

$errors=[];

// validate email format
if(!Validator::email($email)){
    $errors['email']='Please provide a valid email address.';
}

// validate password length (7-255 chars)
if(!Validator::string($password,7,255)){
    $errors['password']='Please provide a password of at least seven characters.';
}

// if there are errors, return to view with errors
if(!empty($errors)){
    return require 'views/registration/create.view.php';
}
```

check database

```php
// continue controllers/registration/store.php

// get database instance
$db=App::resolve(Database::class);

// check if email already exists
$user=$db->query('select * from users where email=:email',[
    'email'=>$email
])->find();

if($user){
    // user already exists, redirect to login page
    header('Location: /login');
    exit();
}
```

insert user and start session

```php
// continue controllers/registration/store.php

// insert new user into database
$db->query('INSERT INTO users (email,password) VALUES (:email,:password)',[
    'email'=>$email,
    'password'=>$password
]);

// store user data in session (auto login)
$_SESSION['user']=[
    'email'=>$email
];

// redirect to home page after success
header('Location: /');
exit();
```

## Middleware intro 
middleware is a layer runs before the controller to check something like authentication or permissions before access the route
we use it to protect routes or control access based on user state (guest or auth)

## Example
in our router when we match the route we check middleware
```php
if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
    if ($route['middleware'] === 'guest') {
        if ($_SESSION['user'] ?? false) {
            header('location: /');
            exit();
        }
    }
    if ($route['middleware'] === 'auth') {
        if (!$_SESSION['user'] ?? false) {
            header('location: /');
            exit();
        }
    }
}
````

guest -> allow only users not logged in
auth -> allow only logged in users

## How we assign middleware to route

```php
$router->get('/register','controllers/registration/create.php')->only('guest');
```

## only() function

this function attach middleware to the last added route

```php
function only($key){
    $this->routes[array_key_last($this->routes)]['middleware']=$key;
    return $this;
}
```

flow:

1. user request route
2. router match uri + method
3. check if route has middleware
4. run middleware logic (guest or auth)
5. if pass -> go to controller
6. if fail -> redirect user

```
```
تمام 👌 زودت شرح بسيط يخلي الفكرة أوضح + خليت كل حاجة مضغوطة وفي **Markdown block واحد** بنفس أسلوبك 👇

````markdown id="mwref8"
## Refactor our Middleware
if i want to add many checks like confirm email, admin role, or any other condition putting all logic inside router will make it messy and hard to maintain so we refactor by separating each middleware into its own class and control them from one central place

## Auth Middleware
this class handle auth check only (single responsibility)
```php
<?php
namespace Core\Middleware;

class Auth
{
    public function handle()
    {
        // if no logged in user redirect to home page
        if (!$_SESSION['user'] ?? false) {
            header('location: /');
            exit();
        }
    }
}
````

## Middleware Manager (Main Class)

this class is responsible for mapping keys to classes and executing the correct middleware

```php
<?php
namespace Core\Middleware;

class Middleware
{
    // static map: key => middleware class
    public const MAP = [
        'guest' => Guest::class,
        'auth'  => Auth::class
    ];

    public static function resolve($key)
    {
        // if no middleware assigned do nothing
        if (!$key) {
            return;
        }

        // get middleware class from map
        $middleware = static::MAP[$key] ?? false;

        if ($middleware) {
            // create instance and run handle method
            (new $middleware)->handle();
        }
    }
}
```

## Usage in Router

instead of writing conditions manually we just call middleware resolver

```php
// inside router dispatch function
if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
    
    // run middleware before controller
    \Core\Middleware\Middleware::resolve($route['middleware']);

    // then load controller
    return require base_path($route['controller']);
}
```

## Why this is better

* clean router (no if conditions mess)
* easy to add new middleware (just create class and add to MAP)
* follow single responsibility principle
* scalable like real frameworks (Laravel style)


```
```
## Make our passwrd Hash
Hash yes , Beacuse it's  one way no  process can do oopsite but (Encryption) you can solve it if you have a key  
```php
$plainPassword = $_POST['password']; 
// Hashed it before save 
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
```
