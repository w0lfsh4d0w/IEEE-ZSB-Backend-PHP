
## What Controller Do?

first, it receives the request and understands what the user wants, for example: the user wants to show the data of a user with id = 5

it understands that from the request

then it does validation, like checking if the number 5 is correct (integer or not)

then it does authorization, like checking if the user is logged in and if they have permission to view this page

then it does data fetching, it connects with the model and asks it to search in the database and fetch the user that has id = 5

then it makes a decision:

* if the user exists, it prepares the data, calls the specific view (profile), and sends the data to it
* if the model returns “data not found”, it redirects the user to a Not Found page

---

## Dynamic Views:

Difference between static and dynamic views

**Static view**:
it is like a printed tablet. The content is written inside and never changes. even if the programmer wants to change something, they must edit the code manually. if you want to show 100 profiles, you will need 100 files

**Dynamic view**:
it is a template. one file contains the main design, but instead of writing specific data, we use variables. when the page runs, the server replaces these variables with the correct data coming from the database

---

## Data Passing

in native PHP, we pass variables by scope

when the controller declares a variable like `$userData` and then requires the view file, the view file can access all these variables

---

## Templating (Headers & Footers):

MVC allows us to create templates (master layouts) or separate pages into partials

we separate duplicate code like a navbar that is repeated in many pages. we put it in an independent file and require it inside the view file

---

## Logic in Views:

we have a main principle called Separation of Concerns

the job of the view should only be displaying and rendering. it should not contain complex logic

if we put logic inside the view:

* **Spaghetti Code**: conflict between HTML, CSS, PHP code, and queries
* it becomes very hard to maintain
* it becomes very hard to test the code or functions when everything is mixed together


