Movies V3
=========

This project will contain all features from [V2](https://github.com/Spezelechse/movies-v2) and add some new ones. The biggest difference to V2 will be that the backend is based on [Zend Framework 2](http://framework.zend.com/) and the fronted on [jquery](http://jquery.com/) and [Bootstrap 3](http://getbootstrap.com/).

Some of the new features will be:
- multilingual (V2 was just german)
- import data from imdb with help of [imdbphp](http://projects.izzysoft.de/trac/imdbphp/wiki/WikiStart)
- responsive(ish) design
- improved database design
 
**Live demo** of the master-branch can be found [here](http://movies-demo.spezelechse.de/) and the login data is admin | Test?123

Please keep it clean ;)

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

------------------------------------------------------------------------------------------------

###Whats planned next?
- continue testing
- build the first release
- implement transfer skript v2 -> v3
- add an alternative pdf view with details
- maybe change the imdb search listing for easier identifying of the searched
- add a detecting for changed userdata (especially userrights)
