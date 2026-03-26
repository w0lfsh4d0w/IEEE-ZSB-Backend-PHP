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
