<?php
	session_start();
	
	require_once "config.inc.php";
	require_once "user.inc.php";
	
	/*
	 * If configuration requires user authentication
	* and no users exist
	*/
	if(!User::users_exist() &&
			Config::$auth == "user")
	{
		header("Location: setup.php");
	}
	
	/*
	 * If configuration requires user authentication
	 * and user is not logged in
	 */ 
	if(!User::is_logged_in())
	{
		$error = false;
		
		// If login details are provided
		if(isset($_POST["email"]) &&
			isset($_POST["pass"]))
		{
			$uid = User::is_valid($_POST["email"], $_POST["pass"]);
			
			if($uid != false)
			{
				User::log_in($uid);
			}
			else
			{
				$error = "<div class=\"alert alert-error\">" .
							"Invalid Username or Password!</div>";
			}
		}
	}
	else
	{
		if(isset($_GET["log_out"]))
		{
			User::log_out();
			header("Location: index.php");
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo Config::$title; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript" src="js/display.js"></script>
	<script type="text/javascript" src="js/sha1.js"></script>
	<script type="text/javascript" src="js/server.js"></script>
	<script type="text/javascript" src="js/settings.js"></script>
	<script type="text/javascript" src="js/tags.js"></script>
	<script type="text/javascript" src="js/bookmarks.js"></script>
	<script type="text/javascript" src="js/interaction.js"></script>
	
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
	<link href="css/custom.css" rel="stylesheet" />
</head>
<body>
<?php if(User::is_logged_in()) { ?>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style="padding-right:20px; padding-left:20px;">
			<a class="brand timeline" href="#"><?php echo Config::$title; ?></a>
			
			<form class="navbar-form">
				<button class="btn timeline" type="button"><i class="icon-list"></i></button>
				<button class="btn" type="button" id="tags"><i class="icon-tags"></i></button>
				<?php if(Config::$auth != "none") { ?>
					<button class="btn" type="button" id="settings"><i class="icon-wrench"></i></button>
				<?php } ?>
			
				<input type="text" id="add-url" placeholder="Add a URL here" />
				<button class="btn" type="button" id="add"><i class="icon-plus"></i></button>
				
				<input type="text" class="typeahead" id="search-titles" placeholder="Search" />
				<button class="btn" type="button" id="search"><i class="icon-search"></i></button>
				
				<?php if(Config::$auth != "none") { ?>
					<a class="btn btn-danger pull-right" href="?log_out">Log Out</a>
				<?php } ?>
			</form>
		</div>
	</div>
	<div class="container-fluid">
		<div id="status"></div>
		<div id="main"></div>
	</div>
	
	<div id="tag_dialog" class="modal hide fade" 
	tabindex="-1" role="dialog" aria-labelledby="Add Tags" 
	aria-hidden="true">
		<div class="modal-body">
			<strong id="bookmark_title"></strong><br /><br />
			Tags (Click to delete)
			<div id="bookmark_tags"></div><br />
			<form class="form-inline">
				<input class="input-block-level type="text" id="search-tags" 
						class="typeahead" placeholder="Tag to add" />
			</form>
		</div>
		<div class="modal-footer">
			<button class="btn" type="button" id="add_tag">
				<i class="icon-plus"></i> Add Tag</button>
			<button id="done_tagging" class="btn" data-dismiss="modal" 
			aria-hidden="true">Done</button>
		</div>
	</div>
<?php } else { ?>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style="padding-right:20px; padding-left:20px;">
			<a class="brand" href="#"><?php echo Config::$title; ?></a>
		</div>
	</div>
	
	<div class="container">
		<form class="form-signin" method="post">
			<h2 class="form-signin-heading">Please sign in</h2>
			<input name="email" type="text" class="input-block-level" placeholder="Email">
			<input name="pass" type="password" class="input-block-level" placeholder="Password">
			<!--
			<label class="checkbox">
				<input type="checkbox" value="remember-me"> Remember me
			</label>
			 -->
			 <?php if($error != false) { echo $error; } ?>
			<button class="btn btn-large" type="submit">Sign in</button>
		</form>
	</div>
<?php } ?>
<?php if(Config::$show_version) { ?>
	<footer class="center">
		<small>
			<a href="https://github.com/jacobzelek/phpmybookmarks">phpMyBookmarks</a> 
			v.<?php echo Version::to_string(); ?>
		</small>
	</footer>
<?php } ?>
</body>
</html>