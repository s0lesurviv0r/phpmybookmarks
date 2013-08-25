<?php

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