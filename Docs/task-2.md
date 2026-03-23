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