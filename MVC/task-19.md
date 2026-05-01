## 1. Model & Database

in MVC, the **Model** is the only part that should talk directly to the database

why?
because we want to keep everything organized and secure

* the model is responsible for data (insert, update, delete, select)
* the controller should not deal with database queries directly
* the view should never touch the database

this gives us:

* **separation of concerns** → each part has one job
* easier maintenance → if database logic changes, we only edit the model
* better security → we control all queries in one place

---

## 2. Sensitive Information (Config Files)

we should not hardcode sensitive data like database password inside main files

instead, we put it in a separate **config file**

why?

* security → if someone sees your code (like on GitHub), they won’t see passwords
* flexibility → you can change database info without editing all files
* environment support → you can have different config for local / production

example:
you may have `config.php` that contains database credentials, and the app just loads it

---

## 3. What is PDO?

PDO (PHP Data Objects) is a way to connect PHP with databases

it is preferred over mysqli because:

* it supports many databases (MySQL, PostgreSQL, SQLite, etc)
* it has built-in support for **prepared statements**
* cleaner and more organized code

so instead of writing different code for each database, PDO gives you one unified way

---

## 4. Prepared Statements & SQL Injection

prepared statements protect your app from SQL Injection

how?

instead of putting user input directly inside the query, we use placeholders

example idea:
we write the query first, then send the data separately

so even if the user enters something malicious like:
`5 OR 1=1`

it will be treated as a normal value, not part of the SQL query

this prevents attackers from changing the query logic

---

## 5. Single Row vs Multiple Rows

**Single row example:**
when a user opens their profile page

* we search by `id = 5`
* we expect only one user
* so we fetch one row

---

**Multiple rows example:**
when showing a list of posts or products

* like homepage with latest posts
* or products in a store

here we expect many results
so we fetch an **array of rows** and loop over them in the view
