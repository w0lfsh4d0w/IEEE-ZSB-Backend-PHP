
## The MVC Pattern:

MVC stands for Model, View, Controller

Model => responsible for managing the data of the application, including the database and business logic. It is the part that deals with data

View : is responsible for what the user sees and interacts with (HTML, CSS, JavaScript)

Controller : is the bridge that takes requests from the user, gets data from the Model, and passes it to the View

---

## Router

Router : is a system that contains rules. These rules connect URLs with what should happen when a user requests a specific URL

for example:

```php id="8p0t5n"
$router->get('/about', 'about.php');
```

this is a rule: when the user requests `/about`, the router checks for this rule and directs the request to `about.php` (the user does not know about the real file)

Traffic Cop => the router is like a traffic cop. It stands and checks what the user requests (which page or URL), then decides the correct direction (the controller) and directs the user to it

---

## Front Controller

when we have a front controller like `index.php`, it means we have a single entry point

we have full control, and instead of duplicate code like configuration and database setup in every single file, we write it once and include it in the controller

any request must pass through the front controller, then the router directs it to the correct controller

---

## Clean URLs

websites use clean URLs because:

* for UX: they are easier to read and share
* for security: you do not expose the architecture of your website (like file names or extensions such as `.php`) to hackers or malicious users

---

## Separation of Concerns

Separation of Concerns means that in our application, every single part does only one job

if we mix SQL inside HTML, it makes our code:

* **Unmaintainable** : imagine one file with thousands of lines of HTML and CSS, and inside it many queries. It becomes easy to break the design and hard to edit

* **Impossible for teamwork** : if we have a frontend developer working on views and a backend developer working on queries, they cannot work efficiently on the same file without conflicts

* **No reusability** : to reuse code, it must do a specific job in a specific place. Then when you need it, you just call it

---

