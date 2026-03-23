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