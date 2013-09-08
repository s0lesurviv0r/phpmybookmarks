<?php
	
/*
phpmybookmarks
Copyright (C) 2013  Jacob Zelek

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once "version.inc.php";
require_once "functions.inc.php";

class Config
{
	// Title to display
	public static $title = "Bookmarks";
	
	// Type of database to store bookmarks (sqlite|mysql)
	// @todo Develop mysql support
	//public static $db_type = "sqlite";
	
	// SQLite3 databse file
	public static $sqlite3_file = "bookmarks.s3db";
	
	// Type of authentication (user|none)
	public static $auth = "user";
	
	/*
	public static $memcached = array(
									"host" => "127.0.0.1",
									"port" => 11211
									);
	*/
	
	// Show version in footer
	public static $show_version = true;
}
?>