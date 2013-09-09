<?php

/*
phpmybookmarks
Copyright (C) 2013  Jacob Zelek <jacob@jacobzelek.com>

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

require_once "databases/sqlite3.inc.php";

class User
{
	private static function hash_pass($pass, $salt)
	{
		return hash("sha512", $pass . $salt);
	}
	
	public static function users_exist()
	{
		$db = new SQLite3_DB();
	
		$sql = "SELECT id FROM users";
		$rows = $db->get_rows($sql);
		
		if(count($rows) == 0)
		{
			return false;
		}
		
		return true;
	}
	
	public static function add_user($email, $pass)
	{
		$db = new SQLite3_DB();
		
		$salt = get_random_string(30);
		$pass_hash = User::hash_pass($pass, $salt);
		
		$sql = "INSERT INTO users (email,pass,salt) VALUES " .
				"('" . SQLite3::escapeString($email) . "','" .
				$pass_hash . "','" . $salt . "')";
		
		return $db->query($sql);
	}
	
	public static function is_valid($email, $pass)
	{
		$db = new SQLite3_DB();
		
		$sql = "SELECT id, pass, salt FROM users " .
				"WHERE email = '" . SQLite3::escapeString($email) . "'";
		
		$row = $db->get_row($sql);
		
		if(isset($row["salt"]) && isset($row["pass"]))
		{
			if(User::hash_pass($pass, $row["salt"]) == $row["pass"])
			{
				return $row["id"];
			}
		}
		
		return false;
	}
	
	public static function change_current_pass($new_pass)
	{
		if(User::is_logged_in() &&
			Config::$auth != "none")
		{
			$db = new SQLite3_DB();
			
			$salt = get_random_string(30);
			$pass_hash = User::hash_pass($new_pass, $salt);
			
			$sql = "UPDATE users SET pass = '" . $pass_hash . "', " .
					"salt = '" . $salt . "' WHERE " .
					"id = " . intval($_SESSION["uid"]);
			
			return $db->query($sql);
		}
		
		return false;
	}
	
	public static function is_logged_in()
	{
		if(Config::$auth == "user")
		{
			if(isset($_SESSION["logged_in"]) &&
					$_SESSION["logged_in"] &&
					isset($_SESSION["uid"]))
			{
				return true;
			}
		}
		else if(Config::$auth == "none")
		{
			return true;
		}
		
		return false;
	}
	
	public static function log_in($uid)
	{
		session_regenerate_id();
		$_SESSION["logged_in"] = true;
		$_SESSION["uid"] = $uid;
	}
	
	public static function log_out()
	{
		$_SESSION = array();
		session_destroy();
	}
}