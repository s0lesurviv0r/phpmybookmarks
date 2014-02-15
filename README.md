phpmybookmarks
==============

Bookmark manager using PHP, SQLite, jQuery, and Bootstrap

Status
------

This application is under heavy development and should not be put in a production environment.

Demo
----

A demo is available at http://demos.jacobzelek.com/phpmybookmarks

This is a public user version. Config::$auth is set to "none".

Installation
------------

Place phpmybookmarks source into a folder in your web root.

Make appropriate changes to config.inc.php.

Make folder containing phpmybookmarks writeable by executing the following while inside the folder:

    $ chmod g+w .

Point your browser to index.php. Database will be automatically created. If you selected $auth = "user" then you will be prompted to enter login details to create a new user.
