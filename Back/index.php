<?php
/**
 * index.php - this contains most of the framework for the back end of BlogDraw (Known as "The Back").
**/
//Here we set our basic requirements, and do some security testing.
  $notLoggedIn = true;
  require_once ('../functions.php');
  require_once ('functions/functions_back.php');
  $dBConnection = connect();
  $dBQuery = "SELECT Cookie,ID FROM `" . DBPREFIX . "_LoginTable` WHERE CHAR_LENGTH(Cookie) > 1;";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedCookie = mb_convert_encoding($row['Cookie'], "UTF-8");
    $safeCookie = mysqli_real_escape_string($dBConnection,$safeCookie);
    if ($returnedCookie == $safeCookie)
      $notLoggedIn = false;
  }
  disconnect($dBConnection); 
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
    <link rel="apple-touch-icon" href="  <?php output_home_link(); ?>/Uploads/apple-touch-icon.png" />
    <link rel="shortcut icon" href="  <?php output_home_link(); ?>/Uploads/favicon.ico" />
    <!-- Here`s where the SEO comes in. -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="  <?php output_head_description(); ?>" />
    <title>The Back -   <?php output_head_title(); ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./bootstrap/css/bootstrap-theme.min.css" />
  </head>
  <body>
<?php
//If the user isn't logged in, boot them to the login page.  Otherwise, show them The Back.
  if ($notLoggedIn == true)
    include ('./page_login.php');
  else
  {
    if(isset($_GET['page']))
      $subPage = htmlspecialchars(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL));
?>
      <nav id="navbar" class="navbar navbar-default navbar-static-top">
      <div class="container-fluid">
        <ul class="nav navbar-nav">
          <li  <?php if(!isset($_GET['page'])){echo ' class="active"';} ?>><a href="  <?php PROTOCOL . URL ?>/Back/" title="The Back"><span class="glyphicon glyphicon-home" aria-hidden="true" aria-label="The Back"></span>&nbsp;The Back</a></li>
          <li  <?php if(isset($_GET['page']) && $subPage == "AddPost"){echo ' class="active"';} ?>><a href="  <?php echo PROTOCOL . URL; ?>/Back/?page=AddPost" title="Write a Post">Write a Post</a></li>
          <li  <?php if(isset($_GET['page']) && $subPage == "EditPost"){echo ' class="active"';} ?>><a href="  <?php echo PROTOCOL . URL; ?>/Back/?page=EditPost" title="View and Edit Posts">View and Edit Posts</a></li>
          <li  <?php if(isset($_GET['page']) && $subPage == "Media"){echo ' class="active"';} ?>><a href="  <?php echo PROTOCOL . URL;?>/Back/?page=Media" title="Add and Edit Media">Add and Edit Media</a></li>
          <li  <?php if(isset($_GET['page']) && $subPage == "Account"){echo ' class="active"';} ?>><a href="  <?php echo PROTOCOL . URL; ?>/Back/?page=Account" title="My Account">My Account</a></li>
          <li  <?php if(isset($_GET['page']) && $subPage == "Register"){echo ' class="active"';} ?>><a href="  <?php echo PROTOCOL . URL; ?>/Back/?page=Register" title="Register a new User">Register a new User</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="  <?php echo PROTOCOL . URL; ?>/Back/?page=Logout" title="Logout">Logout</a></li>
        </ul>
      </div>
    </nav>
<?php
    //Find out what page the user wants to see, and display it in the context of the framework laid out here.
    if(isset($_GET['page']))
    {
      $subPage = htmlspecialchars(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL));
      if ($subPage == "Account")
        require_once ('./page_account.php');
      else if ($subPage == "EditPost")
        require_once ('./page_edit_posts.php');
      else if ($subPage == "AddPost")
        require_once ('./page_add_posts.php');
      else if ($subPage == "Media")
        require_once ('./page_media_control.php');
      else if ($subPage == "Register")
        require_once ('./page_register.php');
      else if ($subPage == "Logout")
        require_once ('./page_logout.php');
      else
        echo '<p>Page: ' . $subPage . ' Not Found.  Please Try Again.</p>';
    }
    else
    {
      echo '<p><strong>Technical Jargon Ahead: Here be dragons. </strong>Our analytics system is unavailable at the moment due to GDPR Compliance. </p>';
      engine_analytics_collector();
    }
  }
?>
    <!-- jQuery and Bootstrap -->
    <script src="./bootstrap/js/jquery-3.4.1.min.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
