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
