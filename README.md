Movies V3
=========

This project will contain all features from [V2](https://github.com/Spezelechse/movies-v2) and add some new ones. The biggest difference to V2 will be that the backend is based on [Zend Framework 2](http://framework.zend.com/) and the fronted on [jquery](http://jquery.com/).

Some of the new features will be:
- multilingual (V2 was just german)
- import data from imdb with help of [imdbphp](http://projects.izzysoft.de/trac/imdbphp/wiki/WikiStart)
- responsive(ish) design
- single page application (the whole page or just parts of it, not sure at the moment :) )
- improved database design

I've never worked with Zend so the inital build will take some time.

------------------------------------------------------------------------------------------------

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

Whats planned next?
- extend the medium form with functions which allow adding new values for genre, publisher, director and type from within the form - done (29.08.14)
- implementing the advanced search for searching with all informations
- adding search links to the show view which allows searching with single detail informations - done (29.08.14)
- adding an alternative list view with detail informations and one with just pictures
- adding functions which let the user edit his own data - done (29.08.14)
- include and use the config data - done (30.08.14)
- include the userrights - done (31.08.14)
- searching a way to translate the table headers - done (31.08.14)
