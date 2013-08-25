<?php
session_start();

require_once "bookmarks.inc.php";
require_once "user.inc.php";

header('Content-type: application/json');

if(User::is_logged_in())
{
	$response = array(
						"success" => false
						);
	
	if(isset($_REQUEST["url"]) && !empty($_REQUEST["url"]) &&
		isset($_REQUEST["title"]) && !empty($_REQUEST["title"]) &&
		 $_REQUEST["action"] == "add_bookmark")
	{
		$url = filter_var($_REQUEST["url"], FILTER_VALIDATE_URL);
		$title = strip_tags($_REQUEST["title"]);
		
		if($url== false)
		{
			$response["reason"] = "url_invalid";
			$response["reason_readable"] = "Invalid URL";
		}
		else
		{
			$response["success"] = Bookmarks::add($url, $title);
			$response["reason"] = ($response["success"]) ? "" : "db_error";
			$response["reason_readable"] =
			($response["success"]) ? "" : "Database Error";
		}
		
		$response["data"]["url"] = $url;
		$response["data"]["title"] = $title;
	}
	else if(isset($_REQUEST["url"]) && !empty($_REQUEST["url"]) &&
			$_REQUEST["action"] == "add_bookmark")
	{
		$url = filter_var($_REQUEST["url"], FILTER_VALIDATE_URL);
		
		if($url == false)
		{
			$response["reason"] = "url_invalid";
			$response["reason_readable"] = "Invalid URL";
		}
		else
		{
			$response["data"]["title"] = false;
			
			// If bookmark does not already exist
			if(!Bookmarks::exists($url))
			{
				$title = get_title_from_url($url);
				
				// If not title can be parsed
				if($title == false)
				{
					$response["success"] = true;
				}
				else
				{
					$response["success"] = Bookmarks::add($url, $title);
					$response["reason"] = ($response["success"]) ? "" : "db_error";
					$response["reason_readable"] = 
							($response["success"]) ? "" : "Database Error";
				}
				
				$response["data"]["title"] = $title;
			}
			else
			{
				$response["success"] = false;
				$response["reason"] = "duplicate";
				$response["reason_readable"] = "URL already exists";
			}
		}
		
		$response["data"]["url"] = $url;
	}
	else if(isset($_REQUEST["bookmark_id"]) && !empty($_REQUEST["bookmark_id"]) &&
			$_REQUEST["action"] == "gen_auto_tags")
	{
		Tags::gen_auto_tags(intval($_REQUEST["bookmark_id"]));
		
		$response["success"] = true;
	}
	else if(isset($_REQUEST["bookmark_id"]) && !empty($_REQUEST["bookmark_id"]) &&
			isset($_REQUEST["tag"]) && !empty($_REQUEST["tag"]) &&
			$_REQUEST["action"] == "remove_tag")
	{
		$bookmark_id = intval($_REQUEST["bookmark_id"]);
		$tag = strip_tags($_REQUEST["tag"]);
	
		if(Tags::remove($bookmark_id, $tag) == false)
		{
			$response["success"] = false;
			$response["reason"] = "db_error";
			$response["reason_readable"] = "Database Error";
		}
		else
		{
			$response["success"] = true;
		}
	}
	else if(isset($_REQUEST["bookmark_id"]) && !empty($_REQUEST["bookmark_id"]) &&
			isset($_REQUEST["tag"]) && !empty($_REQUEST["tag"]) &&
			$_REQUEST["action"] == "add_tag")
	{
		$bookmark_id = intval($_REQUEST["bookmark_id"]);
		$tag = strip_tags($_REQUEST["tag"]);
		
		if(!empty($tag))
		{
			if(Tags::exists($bookmark_id, $tag) == false)
			{
				if(Tags::add($bookmark_id, $tag) == false)
				{
					$response["success"] = false;
					$response["reason"] = "db_error";
					$response["reason_readable"] = "Database Error";
				}
				else
				{
					$response["success"] = true;
					$response["data"]["tag"] = $tag;
				}
			}
			else
			{
				$response["success"] = false;
				$response["reason"] = "duplicate";
				$response["reason_readable"] = "Tag already exists for URL";
			}
		}
		else
		{
			$response["success"] = false;
			$response["reason"] = "empty_tag";
			$response["reason_readable"] = "Empty tag";
		}
	}
	else if($_REQUEST["action"] == "get_bookmarks")
	{
		$search = (isset($_REQUEST["search"])) ? $_REQUEST["search"] : "";
		$limit = (isset($_REQUEST["limit"])) ? $_REQUEST["limit"] : 100;
		$tags = (isset($_REQUEST["tags"]) && !empty($_REQUEST["tags"]))
				? preg_split("/\:/", $_REQUEST["tags"]) : array();
		
		$bookmarks = Bookmarks::get_bookmarks($search, $limit, $tags);
		
		if($bookmarks == false)
		{
			$response["success"] = false;
			$response["reason"] = "db_error";
			$response["reason_readable"] = "Database Error";
		}
		else
		{
			$response["success"] = true;
			$response["data"] = $bookmarks;
		}
	}
	else if($_REQUEST["action"] == "get_tags")
	{
		$tags = Tags::get_all_tags();
		
		if($tags == false)
		{
			$response["success"] = false;
			$response["reason"] = "db_error";
			$response["reason_readable"] = "Database Error";
		}
		else
		{
			$response["success"] = true;
			$response["data"]["tags"] = $tags;
		}
	}
	else if($_REQUEST["action"] == "get_current_id")
	{
		$current_id = Bookmarks::get_current_id();
		
		if($current_id == false)
		{
			$response["success"] = false;
			$response["reason"] = "db_error";
			$response["reason_readable"] = "Database Error";
		}
		else
		{
			$response["success"] = true;
			$response["data"]["current_id"] = $current_id;
		}
	}
	else if($_REQUEST["action"] == "remove_bookmark" && isset($_REQUEST["id"]))
	{	
		if(Bookmarks::delete(intval($_REQUEST["id"])) == false)
		{
			$response["success"] = false;
			$response["reason"] = "db_error";
			$response["reason_readable"] = "Database Error";
		}
		else
		{
			$response["success"] = true;
		}
	}
	else if(isset($_REQUEST["typeahead"]) &&
			isset($_REQUEST["element"]) &&
			$_REQUEST["action"] == "get_typeahead_options" &&
			$_REQUEST["element"] == "search-titles")
	{
		$titles = array("options" => array());

		//@todo Check for DB errors here
		foreach(Bookmarks::get_typeahead($_REQUEST["typeahead"]) as $title)
		{
			$titles["options"][] = $title["title"];
		}
	
		$response["success"] = true;
		$response["data"] = $titles;
	}
	else if(isset($_REQUEST["typeahead"]) &&
			isset($_REQUEST["element"]) &&
			$_REQUEST["action"] == "get_typeahead_options" &&
			$_REQUEST["element"] == "search-tags")
	{
		$tags = array("options" => array());
	
		//@todo Check for DB errors here
		foreach(Tags::get_typeahead($_REQUEST["typeahead"]) as $tag)
		{
			$tags["options"][] = $tag["tag"];
		}
	
		$response["success"] = true;
		$response["data"] = $tags;
	}
	else if(isset($_REQUEST["new_pass_confirm"]) &&
			isset($_REQUEST["new_pass"]) &&
			$_REQUEST["action"] == "change_pass")
	{
		//$old_pass = $_REQUEST["old_pass"];
		$new_pass_confirm = $_REQUEST["new_pass_confirm"];
		$new_pass = $_REQUEST["new_pass"];
		
		if($new_pass == $new_pass_confirm)
		{
			$response["success"] = User::change_current_pass($new_pass);
		}
		else
		{
			$response["reason"] = "new_pass_mismatch";
			$response["reason_readable"] = "New passwords don't match";
		}
	}
	
	if((!isset($response["reason"]) && !$response["success"]) ||
		!isset($_REQUEST["action"]))
	{
		$response["reason"] = "unknown";
		$response["reason_readable"] = "Unknown failure - " .
		"Please submit bug report to developer";
	}
	
	echo json_encode($response);
}
else
{
	echo json_encode(array("success" => false, 
							"reason" => "not_logged_in",
							"reason_readable" => "You are not currently logged in"));
}

?>