## Class Vs Object

## Class
class is a design (blueprint)  it is not a real thing you can touch  
it only describe properties and behaviors  

## Object
object is the real instance created from class   it is the actual thing you can use in your program  

## Example
real world example:

class → blueprint of iPhone  
it contains design, specs, features  

object → real device produced from that blueprint  
many devices can be created from same class  

## Important Idea
- one class → many objects  
- class = template  
- object = instance  

___
## This Vs Self

this : refers to an instance from our class
when we use it? if you want to access properties or non-static methods
to access it use `->` operator

self : refers to the class, not an instance from it
when we use it? if you want to access constants or static members that all objects share
to access it use Scope Resolution Operator `::`

---

## Access Modifiers

we use it to save your data, it is like a guard

public : when you use public, you can access it from inside the class, inherited class, and outside the class

protected : when you use protected, you can access it from inside the class and inherited class only

private : when you use private, you can access it from inside the class only

why we use private for property? imagine we have a bank system and the balance property is public
anyone can access it from outside and change it, this is a disaster

---

## Typed Properties

it started in PHP 7.4
when we specify a type like int, string, bool ...
no one can put a string in place where we expect a number like age

```php
class User {
    public int $age;
}

$user = new User();
$user->age = "25 years";
```

instead of an error in the future, this will lead to a fatal error and this is best

---

## Constructor Methods

this is a magic method, it is called automatically when you use `new`
we use it to initialize an object like giving it initial values or connecting to a database

useful to pass arguments in constructor instead of creating an object and setting its properties in other lines, we can do it in one line like this

```php
class Car {
    public string $model;

    public function __construct($modelName) {
        $this->model = $modelName;
    }
}

$myCar = new Car("Toyota");
```

---
