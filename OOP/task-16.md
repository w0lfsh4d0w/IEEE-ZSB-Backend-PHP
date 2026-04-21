
## Traits

Trait is a collection of functions that you can use in a class. It is not a class, but it is a method for code reuse in different classes that do not share the same parent class

we use it when we want to share a specific method or multiple methods across a number of classes
to keep the principle of DRY

```php
trait Logger {
    public function log($message) {
        echo "Logging message: $message";
    }
}

class User {
    use Logger; 
}

class Post {
    use Logger; 
}
```

---

## Namespaces

Namespace : is like a container or virtual folder that organizes classes. For example, we may have two `index.php` files in different folders

it prevents conflicts by giving us a unique path for every class

like:

```php
namespace ExternalLib;
class Image {}

namespace MyProject;
class Image {}

$obj1 = new \ExternalLib\Image();
$obj2 = new \MyProject\Image();
```

---

## Autoloading

autoload : it is an automatic search for any class file and includes it automatically when you type the keyword `new` to create an object from this class

How it saves time?

* decreases boilerplate code, you don’t have to write hundreds of lines
* it loads files only when needed, which saves memory

---

## Magic Method get & set

it is used to deal with properties that do not exist or are not visible

get : called automatically when you try to read a private or non-existing property
set : called automatically when you try to assign or modify a value for a private or non-existing property

```php
class Student {
    private $data = [];

    public function __set($name, $value) {
        $this->data[$name] = $value; 
    }

    public function __get($name) {
        return $this->data[$name] ?? "Not Found";
    }
}

$s = new Student();
$s->age = 20; 
echo $s->age; 
```

---

## Static Methods and Properties

when a method or property is declared as static, it means it belongs to the class itself, not to a specific object

* it has one place in memory and all instances share it

you don’t have to use the keyword `new` to access static methods or properties. You can access them using the class name followed by the Scope Resolution Operator and the member name

```php
class MathHelper {
    public static $pi = 3.14;

    public static function square($num) {
        return $num * $num;
    }
}

echo MathHelper::$pi; 
echo MathHelper::square(4);
```

---

