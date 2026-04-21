## Inheritance
the most important benfit id Code Reusability and decrease dublication instead of write all properties and methods in every class we make a standard class called Parent but the common properties and methode in it and use in child class by specific way 
```php
class Employee {
    public $name;
    
    public function work() {
        return "The employee is working.";
    }
}


class Developer extends Employee {
    public function coding() {
        return "The developer is writing code.";
    }
}

```
___

## Final Keyword 
what happen when we use it  with 
class : prevent any other class to inherit from it (Dont have childs )
Methods : prevent subclasses from do Overriding for this method (NO Overridng )
why ? 
for security : prevent any one from change sensitive Function behavior like function for Authentication 
Tight design: When you design a class that performs a very specific function, you don't want anyone to tamper with its structure in the future.

___
## Overriding Methods
Override ? mean the child class make overwrite for a fun is already exist in parent class ti implement it by another way to do his needs with the same name 

by use keyword parent:: then function name this is useful for if you want to implement 
the parent code addition to child code 
```php
class Animal {
    public function makeSound() {
        return "Some generic sound";
    }
}

class Dog extends Animal {
    //Do  Overriding
    public function makeSound() {
        // call Parent method first 
        $original = parent::makeSound(); 
        return $original . " but specifically: Woof! Woof!";
    }
}
```
## Abstract Class vS Interface

**1. What is the main difference between an Abstract Class and an Interface?**
* **Abstract Class (Identity / "Is-A"):** It defines *what* a class is. It can contain actual working code, variables (state), and abstract methods. It is used to share core logic among related classes.
* **Interface (Capability / "Can-Do"):** It defines *what* a class can do. It acts strictly as a contract containing only method names (no code or variables), forcing any class (even unrelated ones) to implement those specific behaviors.

**2. Can a class implement multiple interfaces?**
* **Yes.** A class can implement multiple interfaces simultaneously, whereas it can only inherit from one abstract class.

## Polymorphism 
The abbility of differnt objects to do the same function (same name ) by the diifernt implentiaon depend on the behaviour of each object 
```php

class Animal:
    def speak(self):
        pass 

class Dog(Animal):
    def speak(self):
        return "Woof! Woof!"

class Cat(Animal):
    def speak(self):
        return "Meow!"


my_dog = Dog()
my_cat = Cat()

make_animal_speak(my_dog) 
make_animal_speak(my_cat) 

```