
# : Keeping Code Private


## 1. Public vs Private

 wrong: put all files in one folder  
 right: separate logic from public files

 you must make your document root is public in this case mo body can access the private folder   

- `public/` → index.php + assets  
- `private/` → functions + classes + config  

```php
// public/index.php
include '../private/functions.php';

hey();
````

✔️ user cannot go  document root
✔️ only server can access private files

---

## 🚫 2. Directory Listing

if no index → server shows all files
this is a security risk

### solution:

### 1) manual

put empty `index.php` in every folder

### 2) professional

create file called .htaaccess and put in  it  => Options -Indexes


 instead of files → user gets 403 forbidden

##  . File Extensions

 wrong:
if you put information like config in file his exetension is  txt or json or any thing 
anyone can open and see data

 right:

always make the exetension is php 
```php 
<?php

$db_user = "root";
$db_pass = "password";
```

✔️ php runs on server → user cannot see it

---
___



#  Include & Code Injection (LFI)

## Problem
we take user input from url and pass it directly to include
```php
$page=$_GET['page'];
include($page);
````

this is very dangerous because include will execute any php code inside file not just read it
user now control what file server run → this is LFI and can lead to RCE

## Attack Scenarios

1. directory traversal

```bash
?page=../../../../logs/error.log
```

attacker reach system files

2. log poisoning
   attacker inject php code in logs → then include it → code executed

3. malicious upload
   upload image with php code inside

```php
<?php system('ls'); ?>
```

then include image → server execute code

## Solution

### 1. force extension (weak)

```php
include($_GET['page'].'.php');
```

not safe 100%

### 2. process uploads

recreate image (resize/crop) to remove hidden code

### 3. Whitelist (best solution)

only allow specific files

```php
$allowed=glob("*.php");

$page=$_GET['page'].'.php';

if(in_array($page,$allowed)){
    include($page);
}else{
    echo "error";
}
```

### 4. use safe read instead of include

```php
echo file_get_contents('file.txt');
```

this read as string not execute code



```
```

تمام 👌 ده نفس أسلوبك + مضغوط + فكرة **problem / solution** في **Markdown block واحد** 👇

````markdown id="mep4x1"
## Multiple Entry Point

## Problem
if you have many files like index.php, about.php, contact.php user can access them directly  
this means many entry points → many doors  
if you have bug or security issue you must fix it in every file → hard to maintain  

## Solution
make single entry point (index.php)  
all requests go through it → then it decide which page to load  

## Structure
move all pages to folder (ex: includes/) and keep only index.php outside  
also add index.php inside folder to prevent directory listing  

## Simple Router
```php
<?php
// get page from url or default = home
$page=isset($_GET['page'])?$_GET['page']:'home';

// folder path
$folder="includes/";

// build file path
$filename=$folder.$page.".php";

// get all allowed files
$files=glob($folder."*.php");

// check if requested file exists in allowed list
if(in_array($filename,$files)){
    include $filename;
}else{
    include $folder."404.php";
}
````

## Why this is better

* one entry point → easier control
* more secure (no direct access to files)
* fix bugs in one place
* prevent LFI using whitelist (glob)

flow:

1. user request ?page=about
2. index.php catch request
3. check if file allowed
4. include file or show 404

```
```
___

## URL Rewriting

## Problem
our url look like this  
website.com/index.php?page=login  
this is not clean and not professional  
also it expose implementation details (like index.php and query params) to user  

we want clean url like  
website.com/login  

## Solution
we use .htaccess file (apache) to rewrite all requests to index.php  
this file run before php code  

## Idea
if user request /login and this file not exist → redirect to index.php  
then index.php send request to router  
router handle it and load correct page  

## .htaccess Example
```apache
RewriteEngine On

# if file or folder not exist
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# redirect all to index.php
RewriteRule ^(.*)$ index.php [QSA,L]
````

## Router

now index.php receive request and extract uri

```php
$uri=parse_url($_SERVER['REQUEST_URI'])['path'];
```

then match with our routes

```php
$router->get('/','index.php');
$router->get('/about','about.php');
$router->get('/contact','contact.php');
$router->get('/login','login.php');
```

## Flow

1. user go to /login
2. apache (.htaccess) redirect to index.php
3. index.php get uri (/login)
4. router match it with routes
5. correct page loaded

## Why this is better

* clean and professional urls
* hide internal structure (more secure)
* better for SEO
* central control (single entry point)

```
```
## Refactor The Code

## Problem
we repeat database connection code in every page  
this lead to duplicated code → hard to maintain and update  
if we change config we must change it in every file  

## Solution
make a central file (functions.php) contain reusable functions  
so we write code once and use it anywhere  

## functions.php
```php
<?php

// connect to database
function connect(){
    $db_server="localhost";
    $db_user="root";
    $db_pass="";
    $db_name="security_db";

    $con=mysqli_connect($db_server,$db_user,$db_pass,$db_name);

    return $con;
}

// general function to read data
function db_read($query){
    $con=connect();

    $result=mysqli_query($con,$query);

    if(!$result){
        return false;
    }

    $data=[];

    while($row=mysqli_fetch_assoc($result)){
        $data[]=$row;
    }

    return $data;
}
````

## Use in Entry Point

we connect functions file with index.php (single entry point)

```php id="q1mf8z"
require "../private/includes/functions.php";
```

## Important Note

we use require not include
because this file is critical → if not found stop execution

## Why this is better

* no duplicated code
* easier to maintain
* reusable functions
* cleaner structure

flow:

1. index.php load functions.php
2. any page use connect() or db_read()
3. database logic centralized

```
```
