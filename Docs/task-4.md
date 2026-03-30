
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
