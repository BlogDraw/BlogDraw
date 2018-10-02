<?php
/**
 * index.php - this contains most of the framework for the back end of BlogDraw (Known as "The Back").
**/
//Here we set our basic requirements, and do some security testing.
	$NotLoggedIn = true;
	require_once ('../functions.php');
	require_once ('./functions_back.php');
	$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
	if (!$DBConnection)
	{
		die('Could not connect to database.  Please try again later.');
	}
	$DBQuery = "SELECT Cookie,ID FROM `" . DBPREFIX . "_LoginTable` WHERE CHAR_LENGTH(Cookie) > 1;";
	$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
	while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
	{
		$ReturnedCookie = mb_convert_encoding($Row['Cookie'], "UTF-8");
		$SafeCookie = mysqli_real_escape_string($DBConnection,$SafeCookie);
		if ($ReturnedCookie == $SafeCookie){$NotLoggedIn = false;}
    }
    mysqli_close($DBConnection); 
//Below, we start our UI
?>
<!DOCTYPE html>
<html lang="en">
	<head>		
		<!-- Enable utf8 reading -->
		<meta charset="UTF-8" />
		<!-- Enable Mobile-first Optimization -->
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="MobileOptimized" content="400" />
		<meta name="HandheldFriendly" content="True" />
		<!-- Enable IE/Edge Standards mode -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- Use An Apple Touch Icon and Favicon -->
		<link rel="apple-touch-icon" href="<?php output_home_link(); ?>/Uploads/apple-touch-icon.png" />
		<link rel="shortcut icon" href="<?php output_home_link(); ?>/Uploads/favicon.ico" />
		<!-- Here`s where the SEO comes in. -->
		<meta name="robots" content="noindex, nofollow">
		<meta name="description" content="<?php output_head_description(); ?>" />
		<title>The Back - <?php output_head_title(); ?></title>
		<!-- Bootstrap -->
		<link rel="stylesheet" href="./bootstrap-3.3.7-dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="./bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" />
	</head>
	<body>
<?php
//If the user isn't logged in, boot them to the login page.  Otherwise, show them The Back.
	if ($NotLoggedIn == true)
	{
		include ('./page_login.php');
	}
	else
	{
		if(isset($_GET['page']))
		{
			$SubPage = htmlspecialchars(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL));
		}
?>
			<nav id="navbar" class="navbar navbar-default navbar-static-top">
			<div class="container-fluid">
				<ul class="nav navbar-nav">
					<li<?php if(!isset($_GET['page'])){echo ' class="active"';} ?>><a href="<?php PROTOCOL . URL ?>/Back/" title="The Back"><span class="glyphicon glyphicon-home" aria-hidden="true" aria-label="The Back"></span>&nbsp;The Back</a></li>
					<li<?php if(isset($_GET['page']) && $SubPage == "AddPost"){echo ' class="active"';} ?>><a href="<?php echo PROTOCOL . URL; ?>/Back/?page=AddPost" title="Write a Post">Write a Post</a></li>
					<li<?php if(isset($_GET['page']) && $SubPage == "EditPost"){echo ' class="active"';} ?>><a href="<?php echo PROTOCOL . URL; ?>/Back/?page=EditPost" title="View and Edit Posts">View and Edit Posts</a></li>
					<li<?php if(isset($_GET['page']) && $SubPage == "Media"){echo ' class="active"';} ?>><a href="<?php echo PROTOCOL . URL;?>/Back/?page=Media" title="Add and Edit Media">Add and Edit Media</a></li>
					<li<?php if(isset($_GET['page']) && $SubPage == "Account"){echo ' class="active"';} ?>><a href="<?php echo PROTOCOL . URL; ?>/Back/?page=Account" title="My Account">My Account</a></li>
					<li<?php if(isset($_GET['page']) && $SubPage == "Register"){echo ' class="active"';} ?>><a href="<?php echo PROTOCOL . URL; ?>/Back/?page=Register" title="Register a new User">Register a new User</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?php echo PROTOCOL . URL; ?>/Back/?page=Logout" title="Logout">Logout</a></li>
				</ul>
			</div>
		</nav>
<?php
		//Find out what page the user wants to see, and display it in the context of the framework laid out here.
		if(isset($_GET['page']))
		{
			$SubPage = htmlspecialchars(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL));
			if ($SubPage == "Account")
			{
				require_once ('./page_account.php');
			}
			else if ($SubPage == "EditPost")
			{
				require_once ('./page_edit_posts.php');
			}
			else if ($SubPage == "AddPost")
			{
				require_once ('./page_add_posts.php');
			}
			else if ($SubPage == "Media")
			{
				require_once ('./page_media_control.php');
			}
			else if ($SubPage == "Register")
			{
				require_once ('./page_register.php');
			}
			else if ($SubPage == "Logout")
			{
				require_once ('./page_logout.php');
			}
			else
			{
				echo '<p>Page: ' . $SubPage . ' Not Found.  Please Try Again.</p>';
			}
		}
		else
		{
			//require_once ('./page_analytics.php');
			echo '<p><strong>Technical Jargon Ahead: Here be dragons. </strong>Analytics is unavailable at the moment due to GDPR Compliance.  If you have a legitimate reason for collecting this analytic data under GDPR, you can enable it by uncommenting the analytics functions in Back/functions_back.php, uncommenting the engine_analytics_collector function in functions.php, and by uncommenting the reference to page_analytics.php in Back/index.php. </p>';
			engine_analytics_collector();
		}
	}
?>
		<!-- jQuery and Bootstrap -->
		<script src="./bootstrap-3.3.7-dist/js/jquery-3.2.1.min.js"></script>
		<script src="./bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	</body>
</html>
