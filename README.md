Movies V3
=========

Since this is my first Zend project, please tell me if I did something wrong or it could be done better/easier. :)

This project will contain all features from [V2](https://github.com/Spezelechse/movies-v2) and add some new ones. The biggest difference to V2 will be that the backend is based on [Zend Framework 2](http://framework.zend.com/) and the fronted on [jquery](http://jquery.com/) and [Bootstrap 3](http://getbootstrap.com/).

Some of the new features will be:
- multilingual (V2 was just german)
- import data from imdb with help of [imdbphp](http://projects.izzysoft.de/trac/imdbphp/wiki/WikiStart)
- responsive(ish) design
- improved database design
 
**Live demo** of the master-branch can be found [here](http://movies-demo.spezelechse.de/) and the login data is admin | Test?123 ( Please keep it clean ;) )

------------------------------------------------------------------------------------------------

###Install

1. Upload the files, contained in the Code directory, to your webserver
2. Import the 'movies_v3_db.sql' file into your database (this will create all needed tables and inital entries)
3. Create a file named 'local.php' under config/autoload/ and add the following code (just replace 'user' and 'pword' with the data needed for your database access):

 ```php
<?php
return array(
  	'db' => array(
          'username' => 'user',
          'password' => 'pword',
      ),
);
 ```
 
4. Edit the 'global.php' file in the same directory and set the right database name in line 17 (replace 'movies_v3' with your database name).
 
 ```php
'dsn' => 'mysql:dbname=movies_v3;host=localhost',
  ```

5. Thats it. Now you can login with the username: admin and password: Test?123

**Info**: The main file (index.php) is contained in the public directory and not the root. Take care of this while setting up your domain or vhost.

------------------------------------------------------------------------------------------------

###Updates

Update 27.08.14
The first build is now ready. Not everything is working but the main functionality does.

Whats working at the moment:
- Medium
  - add, edit, update
  - list, filter, pagenation
  - show details
- User
  - add, edit, update, list
  - login, logout, remember
- Config
  - list and edit (but the data is not used at the moment)
- General
  - Bootstrap 3 Layout (mobile version tested with a Sony Xperia T)
  - multilingual (everything is available in german and english)
  - all inputs are validated and filtered and outputs are escaped

All lists/tables (except the one within the show view) are build with help of dudapiotres [ZfTable](https://github.com/dudapiotr/ZfTable/) module. Which is awesome, easyish to handle and grants a lot of nice features beside just listing data.

Creating the thumbnails of the Medium Cover made simple with help of the [SimpleImage](http://www.white-hat-web-design.co.uk/blog/resizing-images-with-php/) class from Simon Jarvis.

Update 29.08.14
- added search links to the show view which allows searching with single detail informations
- added functions which let the user edit his own data
- extended the medium form with functions which allow adding new values for genre, publisher, director and type from within the form

Update 30.08.14
- included the config data and implemented the usage

Update 31.08.14
- included the the userrights and implemented the usage
- table headers are now translatable 

Update 02.09.14
- added an alternative list view with detail informations and one with just pictures/covers

Update 05.09.14
- implemented the advanced search for searching with all informations

Update 14.09.14
- implemented IMDb import
- added new config entries (country and imdb_auto_add)
- changed add popovers, they are now more reuseable

Update 15.09.14
- limited login attempts, now max 3 attempts bevor 1 hour login block
- added config descriptions

Update 22.09.14
- implemented import and export of medium entries
- added year of birth informations for actors
- updated dependencies
- tested on live system + fixed first bugs

Update 23.09.14
- fixed several bugs
- added a big picture overlay to the show view
- added a create PDF function based on [dompdf](https://github.com/dompdf/dompdf) and Raymond Kolbes [zend2 wrapper modul](https://github.com/raykolbe/DOMPDFModule)
- added order by name to selects
- dependencies are now completely on github (had an error while installing on my webspace, no terminal and composer available)

Update 25.09.14
- fixed more bugs (thanks to Buzzard for finding them :) )
- cleaned the code
- added feedback for missing userrights

Update 29.09.14
- Added required marks to create and edit forms
- Wrote install instructions
- Built the first release

Update 30.09.14
- Changed the import/export type from json string to file
- Fixed issue: not displaying the placeholder (@ list view 'Cover') when cover file is missing

Update 02.10.14
- Added a detection of changed user data with the target to react on changed userrights. If the user data changed the user while be informed and logged out.

------------------------------------------------------------------------------------------------

###Whats planned next?
- implement transfer skript v2 -> v3
- add an alternative pdf view with details
- maybe change the imdb search listing  (similar to the detail view for media) for easier identifying of the searched item
- maybe change the actor editing from textareas to an editable table
