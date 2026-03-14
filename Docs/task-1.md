# Fundamentals

okay i will take the first part and call it **fundamentals**

it include  
(varables, if, array, associative array, and lampda fun, sperate logic)

the first 3 topics is very easy and we know it

let's focus in the others

---

## associative array

when we store complex data in arrays it is hard to depend on index to call it

we need the **key**  
the key point to the value

and this is the **associative array**

```php
<?php
$Books = [
  [
    // 
    // 'key' => 'value' , This is the genirc method 
    "name" => "full of emptiness",
    "author" => "Dr.Emad R. Osman",
    "read_URL" => "http://example.com"
  ],
  [
    "name" => "The Hobbit",
    "author" => "Dr.Matek G. Well",
    "read_URL" => "http://example.com"
  ]
];
?>
```

This is example of associative array

and we need to call it we call it like this

```php
<?php foreach($Books as $book): ?>
  <li>
    <a href="<?= $book['read_URL']; ?>">
      <?= $book['name']; ?> - <i><?= $book['author']; ?></i>
    </a>
  </li>
<?php endforeach; ?>
```
___
## lambda fun

it is **Anonymous Function**  
it is function without name  
it is flexible

why I said flexible?  
because we can store it in variable or push it as an argument to another function

This is simple example

```php
$sayHello = function($name) {
    return "Hello " . $name;
}; // we put semicolon here

echo $sayHello("Ahmed");
```

the power of it appears with arrays and processes that need callback

like this

```php
$ages = [15, 20, 12, 25, 30];

$adults = array_filter($ages, function($age) {
    return $age >= 18;
});

print_r($adults);
// it will return [20, 25, 30]
```
___
## separate logic from the view

when we put the php code (data, filters, function) with html code it is a mess  
and it will be very hard to read and maintain the code

and our goal is to rearrange that mess by separating

The solution is **Separation of Concerns**

the meaning of that is to separate the logic (php) away from the presentation (html)

and we will have two files like:

- `index.php` (logic)
- `index.view.php` (presentation)

the important thing is: who is calling whom

the answer is the logic will call the view

and how they see each other

we put in the logic code a call or a copy from the view code by calling it like this

```php
require 'index.view.php';
```
___
