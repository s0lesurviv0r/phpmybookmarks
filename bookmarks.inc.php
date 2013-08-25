<?php

require_once "databases/sqlite3.inc.php";
require_once "tags.inc.php";

class Bookmarks
{
	// Get id of latest bookmark
	public static function get_current_id()
	{
		$db = new SQLite3_DB();
		$sql = "SELECT MAX(id) as id FROM bookmarks";
		$row = $db->get_row($sql);
		return $row["id"];
	}
	
	public static function get_ids()
	{
		$db = new SQLite3_DB();
		$sql = "SELECT DISTINCT id FROM bookmarks";
		$rows = $db->get_rows($sql);
		
		$ids = array();
		
		foreach($rows as $row)
		{
			$ids[] = $row["id"];
		}
		
		return $ids;
	}
	
	public static function delete($id)
	{
		$db = new SQLite3_DB();
		$sql = "DELETE FROM bookmarks WHERE id = " . intval($id) . "; " .
				"DELETE FROM tags WHERE bookmark_id = " . intval($id) . ";";
		
		return $db->query($sql);
	}
	
	public static function get_bookmark($id)
	{
		$db = new SQLite3_DB();
		$sql = "SELECT * FROM bookmarks WHERE id = " . intval($id);
		$row = $db->get_row($sql);
		return $row;
	}
	
	public static function get_bookmarks($search="", $limit=100, $tags=array())
	{
		$db = new SQLite3_DB();
		
		$sql = "SELECT * FROM bookmarks " .
				"WHERE title LIKE " .
				"'%" . SQLite3::escapeString($search) . "%' ";
		
		if(!empty($tags))
		{
			$ids = Tags::get_ids($tags);
			
			$sql .= "AND id IN (" . implode(",", $ids) . ") ";
		}
		
		$sql .=	"ORDER BY id DESC " .
				"LIMIT 0," . $limit;
		
		$rows = $db->get_rows($sql);
		
		$bookmarks = array();
		
		$count = 0;
		$more_data = false;
		
		foreach($rows as $row)
		{
			$id = $row["id"];
			$tags = Tags::get_tags($id);
			
			$row["tags"] = $tags;
			
			$bookmarks[] = $row;
			
			$count++;
			
			if($count >= $limit)
			{
				$more_data = true;
				break;
			}
		}
		
		return array("more" => $more_data,
					"result_count" => $count,
					"bookmarks" => $bookmarks);
	}
	
	public static function get_typeahead($search)
	{
		$db = new SQLite3_DB();
		
		$sql = "SELECT DISTINCT title FROM bookmarks " .
				"WHERE title LIKE " .
				"'%" . SQLite3::escapeString($search) . "%'";
		return $db->get_rows($sql);
	}
	
	public static function exists($url)
	{
		$db = new SQLite3_DB();
		
		$sql = "SELECT url FROM bookmarks " .
				"WHERE url = '" . SQLite3::escapeString($url) . "'";
		
		return (count($db->get_row($sql)) == 0) ? false : true;
	}
	
	public static function add($url, $title)
	{
		$db = new SQLite3_DB();
		
		$sql = "INSERT INTO bookmarks (url,title) " .
				"VALUES ('" . SQLite3::escapeString($url) . "'," .
				"'" . SQLite3::escapeString($title) . "')";
		
		return $db->query($sql);
	}
}