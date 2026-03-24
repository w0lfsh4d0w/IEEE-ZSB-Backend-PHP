## Render Note and Notes Page

The important thing in adding a page is the **path**.

and there are steps:

first, the **route** → this is what the user writes in the browser.

the router takes this request and links it to a controller like this:

```php
'/notes' => 'controllers/notes.php',
```

the **controller** is responsible for data processing.

and the controller passes this data to the **view page**  
and renders it in the browser for the user.
___
## Introduction to Authorization

Now in our app, we fetch the note depending on the **ID** in the query string.

our problem is that there is no **authorization**.  
anyone who has the note ID can see it, even if he didn’t write it.

to solve this:

we need to check the **user ID** too.

and we have 3 cases:

- if the note exists and the user ID is correct → view the note  
- if the note does not exist → we don’t need to check the other condition → **404 Not Found**  
- if the note exists but the user ID is not equal to the target user → **403 Forbidden**

```php
$note = $db->query('SELECT * FROM notes WHERE id = :id', [
    'id' => $_GET['id']
]);

if (! $note) {
    abort(404); // Not Found Page
}

$currentUserId = 1;

if ($note['user_id'] !== $currentUserId) {
    abort(403); // Not allowed to see this page
}
```

and we can refactor this logic into a class  
and make constants for these codes (like 403, 404)  
and call them anywhere we want.

this is called **refactoring**.
___

## Refactor Our Code

this is our code of the `query` function:

```php
function query($query, $params = [])
{
    $statement = $this->connection->prepare($query);
    $statement->execute($params);

    return $statement;
}
```

this function returns a **PDOStatement object**, and this is an internal class in PHP, so we can’t control it  
or add the methods we need.

the solution is to return an instance of our **Database class** instead.

like this:

```php
function query($query, $params = [])
{
    $this->statement = $this->connection->prepare($query);
    $this->statement->execute($params);

    return $this;
}
```

in this case, we have an instance of our class, and it can access any function we want to add to the **Database class**.

we can add some functions like this:

```php
<?php

class Database {

    public function get() {
        return $this->statement->fetchAll();
    }

    public function find() {
        return $this->statement->fetch();
    }

    public function findOrFail() {
        $result = $this->find();

        if (! $result) {
            abort(Response::NOT_FOUND);
        }

        return $result;
    }
}
```

and i can use these functions to fetch data, fetch notes, or fetch one note,  
and check if the note exists or not.

---

in our controller, we used to add an `if` condition to check the authorization,  
and if we want to do it in another page, we will copy it, and this becomes hard-coded.

so i will make a function to check authorization and call it when i need it.

like this:

```php
function authorize($condition, $status = Response::FORBIDDEN) {
    if (! $condition) {
        abort($status);
    }
}
```

---

### How our controller looks after these changes

```php
<?php

$currentUserId = 1;

$note = $db->query('SELECT * FROM notes WHERE id = :id', [
    'id' => $_GET['id']
])->findOrFail(); // now query function returns an instance of our class

authorize($note['user_id'] === $currentUserId); // call authorization

require "views/note.view.php";
```

---

our code becomes more:

- clean  
- flexible  
- scalable

## Forms and Request Method

when the user needs to add a new note, this is an input case.  
when the user gives us input, this is the place where we need a **form**.

what is a form?  
a user interface component, typically a window or a document, containing interactive controls such as text fields, buttons, and checkboxes, designed to collect user input.

```html
<form>
	<label for="body">Note Body:</label>
	<textarea id="body" name="body"></textarea>

	<button type="submit">Create</button>
</form>
```

after we collect the user input, what happens?

there are two important things in forms:

---

### 1. Request Methods (GET / POST)

#### GET

we use it to **fetch pages** or **search**.  
this is a safe process. if you reload the page 1000 times, it will not change anything in the database.

```html
<form method="GET">
</form>
```

---

#### POST

we use it to **send data**, like adding data to the database.  
it is **not idempotent**, because sending the data multiple times will change the database multiple times.

```html
<form method="POST">
</form>
```

---

### 2. Where does the data go?

```html
<form action="note.php">
</form>
```

the `action` attribute decides where the data will go.  
if we add a page, it will go to that page.  
if we do not specify a page, the default is the **same page**.

---

### Handling the Request in PHP

in the target page, we do something like this:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // we can access the data using superglobals
    var_dump($_POST); // to see all data in the array
    die(); // stop the code to show the result
}
```

this is just to clarify the concept.

---

### How we access form data

we deal with the data using the **name attribute** in the input.

```html
<form>
	<label for="body">Note Body:</label>
	<textarea id="body" name="body"></textarea>

	<button type="submit">Create</button>
</form>
```

this:

```
name="body"
```

is the attribute we use to handle every single input.
This is the steps to add page to create note in genral concept 
___

## Always Escape Untrusted Input

now we get input from the user, but by default we should assume the user may try to damage the system  
or add HTML or JavaScript code instead of a normal note.

```html
<script>alert('Hi from JavaScript');</script>
```

like this.

if we save this in our database,  
when we open the notes page, the browser will read this code and execute it.

and anyone who visits the page will see a pop-up window (alert).

this is called **XSS Injection**.

this can escalate to:

- stealing cookies  
- redirecting the user to another malicious page  



### The Solution

the solution is to **escape the output**.

we have a PHP function called `htmlspecialchars()`.

it converts special characters that the browser understands  
into HTML entities (safe strings).

___
## Form Validation

to add the first validation like making sure the textarea is not empty,  
we can use:

```html
<textarea name="body" required></textarea>
```

this is **client-side validation**, and it works only in the browser.

and this is not enough.  
anyone who has simple knowledge in the terminal  
can send a request using HTTP like this:

```bash
curl -X POST http://localhost:8888/note/create -d "body="
```

and in this case, he bypasses our validation.

so we need to make **server-side validation**.

we need to check this data in our controller before we send it to the database.

---

we make simple steps:

like initializing an array to store errors,  
and check the data in `$_POST` with our conditions.

```php
$errors = [];

// check it is not empty
if (strlen($_POST['body']) === 0) {
	$errors['body'] = 'A body is required';
}

// check max length
if (strlen($_POST['body']) > 1000) {
	$errors['body'] = 'The body cannot be more than 1000 characters';
}

if (empty($errors)) {
    // if it is empty we will run our query to the database here
} else {
	require 'views/note-create.view.php';
}
```

---

but now we need some feedback for the user to know his mistake.

we go to the view page and do:

```php
<?php if (isset($errors['body'])) : ?>
	<p class="text-red-500 text-xs mt-2">
		<?= $errors['body'] ?>
	</p>
<?php endif; ?>
```

this checks if `$errors['body']` is set and shows it to the user.

---

in case the user enters text longer than the max length,  
we should not make him start from zero.

we allow him to edit his previous input like this:

```php
<textarea id="body" name="body"><?= $_POST['body'] ?? '' ?></textarea>
```

this code checks if `$_POST['body']` exists.  
if it exists, we show it to the user.  
if not, we show an empty string.  
___

## Extract a Simple Validator Class

why do we need a validator class?

imagine we have a register page, add note page, and add articles page.  
it is not logical to add validation functions in every single page.

so we will put them in a class and call it when we need.

like this:

```php
class Validator {
	public static function string($value, $min = 1, $max = INF) {
		$value = trim($value);

		return strlen($value) >= $min && strlen($value) <= $max;
	}
}
```

`trim()` function → removes any spaces before or after the string.  
it solves the problem when the user enters only spaces.

this check:

```php
strlen($value) >= $min && strlen($value) <= $max;
```

does the job of the two functions we wrote before:

- check it is not empty  
- check the length range  

---

### Important thing

this `string` function is a **pure function**.  
it does not depend on anything external and does not use `$this`.

so we make it **static**.

and if we want to validate anywhere,  
we can call the function without creating an instance of the class.

---

### Usage in controller

```php
if (! Validator::string($_POST['body'], 1, 1000)) {
	$errors['body'] = 'A body of no more than 1000 characters is required.';
}
```