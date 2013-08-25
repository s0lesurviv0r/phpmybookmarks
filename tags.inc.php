<?php

require_once "databases/sqlite3.inc.php";
require_once "functions.inc.php";

class Tags
{
	public static function get_typeahead($search)
	{
		$db = new SQLite3_DB();
		
		$sql = "SELECT DISTINCT tag FROM tags " .
				"WHERE tag LIKE '%" . SQLite3::escapeString($search) . "%'";
		return $db->get_rows($sql);
	}
	
	/*
	 * Returns all bookmark ids whose bookmark is tagged with
	 * all tags given
	 */
	public static function get_ids($tags)
	{
		$db = new SQLite3_DB();
		
		$buckets = array();
		
		for($tid=0; $tid < count($tags); $tid++)
		{	
			$sql = "SELECT bookmark_id FROM tags " .
					"WHERE tag = '" . SQLite3::escapeString($tags[$tid]) . "'";
		
			$rows = $db->get_rows($sql);
			
			foreach($rows as $row)
			{
				$buckets[$tid][] = $row["bookmark_id"];
			}
		}
		
		if(count($buckets) == 1)
		{
			return $buckets[0];
		}
		else
		{
			$bookmark_ids = array();
			
			$intersection = array_intersect($buckets[0], $buckets[1]);
			
			for($bid=2; $bid < count($buckets); $bid++)
			{
				$intersection = array_intersect($intersection, $buckets[$bid]);
			}
		
			return $intersection;
		}
	}
	
	public static function get_tags($id)
	{
		$db = new SQLite3_DB();
		
		$sql = "SELECT * FROM tags WHERE bookmark_id = " . intval($id);
		
		$rows = $db->get_rows($sql);
		
		$tags = array();
		
		foreach($rows as $row)
		{
			$tags[] = $row["tag"];
		}
		
		return $tags;
	}
	
	public static function get_all_tags()
	{
		$db = new SQLite3_DB();
	
		$sql = "SELECT tag, COUNT() AS count FROM tags " .
						"GROUP BY tag ORDER BY lower(tag) ASC;";
	
		$rows = $db->get_rows($sql);
	
		$tags = array();
	
		foreach($rows as $row)
		{
			$tags[] = array(
							"tag" => $row["tag"],
							"count" => $row["count"]
							);
		}
	
		return $tags;
	}
	
	public static function exists($bookmark_id, $tag)
	{
		$db = new SQLite3_DB();
	
		$sql = "SELECT tag FROM tags " .
				"WHERE tag = '" . SQLite3::escapeString($tag) . "' " .
				"AND bookmark_id = " . intval($bookmark_id);
	
		return (count($db->get_row($sql)) == 0) ? false : true;
	}
	
	public static function gen_auto_tags($bookmark_id)
	{
		$bookmark = Bookmarks::get_bookmark($bookmark_id);
		
		$domain = get_domain_from_url($bookmark["url"]);
		$title = $bookmark["title"];
		
		$rules = Tags::get_auto_tag_rules();
		
		foreach($rules as $rule)
		{	
			if(!empty($rule["domain_regex"]) &&
				!empty($rule["title_regex"])
			)
			{
				if(preg_match($rule["domain_regex"], $domain) == 1 &&
					preg_match($rule["title_regex"], $title) == 1)
				{
					if(!Tags::exists($bookmark_id, $rule["tag"]))
					{
						Tags::add($bookmark_id, $rule["tag"]);
					}
				}
			}
		}
	}
	
	/*
	 * @todo Use memcached for SELECT query
	 */
	public static function get_auto_tag_rules()
	{
		$db = new SQLite3_DB();
	
		$sql = "SELECT * FROM auto_tags";
	
		$rows = $db->get_rows($sql);
		
		$rules = array();
		
		foreach($rows as $row)
		{
			$rule = array();
			
			$rule["domain_regex"] = $row["domain_regex"];
			$rule["title_regex"] = $row["title_regex"];
			$rule["tag"] = $row["tag"];
			
			$rules[] = $rule;
		}
		
		return $rules;
	}
	
	public static function add($bookmark_id, $tag)
	{
		$db = new SQLite3_DB();
		$sql = "INSERT INTO tags (bookmark_id, tag) " .
				"VALUES (" . intval($bookmark_id) . "," .
				"'" . SQLite3::escapeString($tag) . "')";
		
		return $db->query($sql);
	}
	
	public static function remove($bookmark_id, $tag)
	{
		$db = new SQLite3_DB();
		$sql = "DELETE FROM tags WHERE " .
				"bookmark_id = " . intval($bookmark_id) . " AND " .
				"tag = '" . SQLite3::escapeString($tag) . "'";
	
		return $db->query($sql);
	}
}