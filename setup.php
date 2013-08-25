<?php
	session_start();
	
	require_once "config.inc.php";
	require_once "user.inc.php";
	
	$error = "";
	
	/*
	 * If configuration requires user authentication
	 * and no users exist
	 */ 
	if(!User::users_exist() &&
			Config::$auth == "user")
	{	
		// If new user details are provided
		if(isset($_POST["email"]) &&
			isset($_POST["pass"]) &&
			isset($_POST["pass_confirm"]))
		{
			$email = $_POST["email"];
			$pass = $_POST["pass"];
			$pass_confirm = $_POST["pass_confirm"];
			
			if($pass == $pass_confirm)
			{
				if(filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					if(User::add_user($email, $pass))
					{
						header("Location: index.php");
					}
					else
					{
						$error = "<div class=\"alert alert-error\">" .
								"User could not be added! Possible " .
								"database problem.</div>";
					}
				}
				else
				{
					$error = "<div class=\"alert alert-error\">" .
							"Email is invalid!</div>";
				}
			}
			else
			{
				$error = "<div class=\"alert alert-error\">" .
						"Passwords don't match!</div>";
			}
		}
	}
	else
	{
		header("Location: index.php");
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
	
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
	<link href="css/custom.css" rel="stylesheet" />
</head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style="padding-right:20px; padding-left:20px;">
			<a class="brand" href="#"><?php echo Config::$title; ?></a>
		</div>
	</div>
	
	<div class="container">
		<form class="form-signin" method="post">
			<h2 class="form-signin-heading">Add User</h2>
			<input name="email" type="text" class="input-block-level" placeholder="Email">
			<input name="pass" type="password" class="input-block-level" placeholder="Password">
			<input name="pass_confirm" type="password" class="input-block-level" placeholder="Confirm Password">
			<?php if($error != false) { echo $error; } ?>
			<button class="btn btn-large" type="submit">Add User</button>
		</form>
	</div>
</body>
</html>