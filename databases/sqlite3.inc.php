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

require_once 'config.inc.php';
require_once 'databases/database.inc.php';

class SQLite3_DB extends Database
{
	private $m_handle;
	
	public function __construct()
	{
		$this->open();
		$this->init();
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	/*
	 * See if tables exist and if not create them
	 */
	private function init()
	{
		$rows = $this->get_rows("SELECT name FROM sqlite_master " .
								"WHERE type = 'table' " .
								"AND name = 'bookmarks'");
	
		if(count($rows) == 0)
		{		
			$this->query(
						'CREATE TABLE bookmarks (
				    	"id" INTEGER PRIMARY KEY,
				    	"url" TEXT NOT NULL,
				    	"title" TEXT NOT NULL)'
					);
		}
		
		$rows = $this->get_rows("SELECT name FROM sqlite_master " .
								"WHERE type = 'table' " .
								"AND name = 'users'");
		
		if(count($rows) == 0)
		{
			$this->query(
						'CREATE TABLE users (
						"id" INTEGER PRIMARY KEY,
						"email" TEXT UNIQUE NOT NULL,
						"pass" TEXT NOT NULL,
						"salt" TEXT NOT NULL)'
					);
		}
		
		$rows = $this->get_rows("SELECT name FROM sqlite_master " .
				"WHERE type = 'table' " .
				"AND name = 'tags'");
		
		if(count($rows) == 0)
		{
			$this->query(
					'CREATE TABLE tags (
					"bookmark_id" INTEGER NOT NULL,
					"tag" TEXT NOT NULL)'
					);
		}
		
		$rows = $this->get_rows("SELECT name FROM sqlite_master " .
				"WHERE type = 'table' " .
				"AND name = 'auto_tags'");
		
		if(count($rows) == 0)
		{
			$this->query(
				'CREATE TABLE "auto_tags" (
			    "id" INTEGER PRIMARY KEY,
			    "domain_regex" TEXT,
			    "title_regex" TEXT,
			    "tag" TEXT)'
				);
		}
	}
	
	private function open()
	{
		$this->m_handle = new SQLite3(Config::$sqlite3_file);
	}
	
	private function close()
	{
		$this->m_handle->close();
	}
	
	public function query($sql)
	{
		return $this->m_handle->exec($sql);
	}
	
	public function get_column($sql)
	{
		$results = $this->m_handle->querySingle($sql);
		return $results;
	}
	
	public function get_row($sql)
	{
		$results = $this->m_handle->querySingle($sql, true);
		return $results;
	}
	
	public function get_rows($sql)
	{
		$results = $this->m_handle->query($sql);
		
		$rows = array();
		
		while($row = $results->fetchArray(SQLITE3_ASSOC))
		{
			$rows[] = $row;
		}
		
		return $rows;
	}
}