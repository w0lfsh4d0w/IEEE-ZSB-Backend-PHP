## Refactor Validator (LoginForm)
i notice core folder has general classes (router, database) can be reused in any project but controllers + validation logic are project-specific so we create new folder Http for this logic

problem: validation inside controller breaks Single Responsibility Principle  
solution: move validation to separate class (Form Object)

## Create LoginForm
```php
namespace Http\\Forms;

class LoginForm {
    protected $errors=[]; // protected errors array (cannot be modified outside)

    public function validate($email,$password){
        // validate email
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $this->errors['email']='invalid email';
        }

        // validate password
        if(strlen($password)<7){
            $this->errors['password']='password must be at least 7 chars';
        }

        // return true if no errors
        return empty($this->errors);
    }

    // getter to access errors safely
    public function errors(){
        return $this->errors;
    }
}
````

## Usage in Controller

```php
use Http\\Forms\\LoginForm;

$form=new LoginForm();

// if validation fails
if(!$form->validate($email,$password)){
    return view('login',[
        'errors'=>$form->errors()
    ]);
}

// if success → continue login logic
```

## Why this is better

* controller becomes clean (only flow control)
* validation logic isolated in one place
* follow Single Responsibility Principle
* reusable and scalable (can add RegisterForm, NoteForm, etc)

flow:

1. user send data
2. controller create form object
3. form validate data
4. if fail → return errors
5. if success → continue logic
___

## Extract an Authenticator Class

Now we want to apply the **Single Responsibility Principle** in our controller.  
Our controller performs authentication for the user, and this means the controller is doing more than one job.

We will convert **auth** to a noun (**Authentication**) and create a class to handle it.

```php
// core/Authenticator.php

class Authenticator {

    public function attempt($email, $password) {
        // 1. Search for the user in the database
        // 2. Verify that the password matches

        if ($user_found_and_password_matches) {
            $this->login($user);
            return true; // Authentication successful
        }

        return false; // Authentication failed
    }
}
````

Our `attempt` function calls functions like `login` and `logout` as helper functions, and this is not ideal.
In the authentication class, we should not rely on external functions to complete our logic.

So, we will move the `login` and `logout` functions into our class:

```php
class Authenticator {

    public function attempt($email, $password) {
        // ... previous code
    }

    public function login($user) {
        // Start session and store user data
        $_SESSION['user'] = [
            'email' => $user['email']
        ];
    }

    public function logout() {
        // Destroy session and log the user out
        // Session destroy...
    }
}
```

We will make `attempt` return `true` if authentication is successful, and `false` if it fails.

---

Our redirect code using `header` is repeated multiple times, so we will create a helper function for it:

```php
// functions.php (global helper functions file)

function redirect($path) {
    header("location: {$path}");
    exit();
}
```

---

## Refactoring Duplicate Error Handling

In the controller, we have two blocks of code that are very similar, so we will merge them.

In both form validation and authentication, we redirect the user to the login page and pass an error message.

We will handle this by adding a function inside the `LoginForm` class to store error messages in an errors array:

```php
// LoginForm.php

class LoginForm {
    protected $errors = [];

    // Add a custom error message
    public function error($field, $message) {
        $this->errors[$field] = $message;
    }

    // ... rest of the code
}
```

```
```
___

## Manage Session and Apply PRG Pattern

### Problem

When the user submits wrong credentials, the browser saves the last request as a **POST request**.  
If the user refreshes the page, the POST request will be repeated again.

Imagine this scenario happening on a page responsible for **payment** — this would be a disaster.

---

### Solution: PRG Pattern

What is PRG?

**PRG = POST → Redirect → GET**

- When a POST request happens, we must follow it with a **redirect**.
- After the redirect, a **GET request** is made.
- Now, if the user refreshes the page, only the GET request is repeated — not the POST request.

---

### Handling Errors with Session

Our code was like this:

```php
$_SESSION['errors'] = $formErrors;
````

The problem with this approach is that if the user refreshes the page, the errors will still be displayed.

We want to make it **stateless**, so when the user refreshes, the errors disappear.

---

### Solution: Flash Session

```php
$_SESSION['_flash']['errors'] = $formErrors;
```

Then, in the entry point (after loading the view), we remove the flash data:

```php
unset($_SESSION['_flash']);
```

---

### Create a Session Class

To avoid repeating session-related code everywhere, we create a `Session` class to handle everything related to sessions:

```php
<?php

namespace Core;

class Session
{
    public static function has($key)
    {
        return (bool) static::get($key);
    }

    public static function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        if (isset($_SESSION['_flash'][$key])) {
            return $_SESSION['_flash'][$key];
        }

        return $_SESSION[$key] ?? $default;
    }

    public static function flash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function unflash()
    {
        unset($_SESSION['_flash']);
    }

    public static function flush()
    {
        $_SESSION = [];
    }

    public static function destroy()
    {
        static::flush();
        session_destroy();
        
        $params = session_get_cookie_params();
        setcookie(
            'PHPSESSID',
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}
```
___
