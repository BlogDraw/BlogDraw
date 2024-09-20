<?php
/**
 * index.php - this contains most of the framework for the back end of BlogDraw (Known as "The Control Panel").
 **/
//Here we set our basic requirements, and do some security testing.
  $notLoggedIn = true;
  require_once('../functions.php');
  require_once('functions/functions_back.php');
  $dBConnection = connect();
  $dBQuery = "SELECT Cookie,ID FROM `" . DBPREFIX . "_LoginTable` WHERE CHAR_LENGTH(Cookie) > 1;";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedCookie = cleanHtmlString($dBConnection, $row['Cookie']);
    $safeCookie = cleanHtmlString($dBConnection,$safeCookie);
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
    <link rel="apple-touch-icon" href="<?php output_home_link(); ?>/uploads/apple-touch-icon.png" />
    <link rel="shortcut icon" href="<?php output_home_link(); ?>/uploads/favicon.ico" />
    <!-- Here`s where the SEO comes in. -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="<?php output_head_description(); ?>" />
    <title>The Control Panel - <?php output_head_title(); ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css" />
    <style>
.btn-light{border-color:#BDBDBD;}
.navbar{margin-bottom:2rem;}
.form-control-file{padding-top:9px;}
    </style>
  </head>
  <body>
<?php
//If the user isn't logged in, boot them to the login page.  Otherwise, show them The Control Panel.
  if ($notLoggedIn == true)
    include ('./page_login.php');
  else
  {
    if (isset($_GET['page']))
      $subPage = htmlspecialchars(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL));
?>
      <nav id="navbar" class="navbar navbar-expand static-top navbar-dark bg-dark">
      <div class="container-fluid">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item<?php if (!isset($_GET['page'])){echo ' active';} ?>"><a class="nav-link" href="<?php PROTOCOL . URL ?>/control/" title="The Control Panel">The Control Panel</a></li>
          <li class="nav-item<?php if (isset($_GET['page']) && $subPage == "AddPost"){echo ' active';} ?>"><a class="nav-link" href="<?php echo PROTOCOL . URL; ?>/control/?page=AddPost" title="Write a Post">Write a Post</a></li>
          <li class="nav-item<?php if (isset($_GET['page']) && $subPage == "EditPost"){echo ' active';} ?>"><a class="nav-link" href="<?php echo PROTOCOL . URL; ?>/control/?page=EditPost" title="View and Edit Posts">View and Edit Posts</a></li>
          <li class="nav-item<?php if (isset($_GET['page']) && $subPage == "Media"){echo ' active';} ?>"><a class="nav-link" href="<?php echo PROTOCOL . URL;?>/control/?page=Media" title="Add and Edit Media">Add and Edit Media</a></li>
          <li class="nav-item<?php if (isset($_GET['page']) && $subPage == "Account"){echo ' active';} ?>"><a class="nav-link" href="<?php echo PROTOCOL . URL; ?>/control/?page=Account" title="My Account">My Account</a></li>
          <li class="nav-item<?php if (isset($_GET['page']) && $subPage == "Register"){echo ' active';} ?>"><a class="nav-link" href="<?php echo PROTOCOL . URL; ?>/control/?page=Register" title="Register a new User">Register a new User</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="<?php echo PROTOCOL . URL; ?>/control/?page=Logout" title="Logout">Logout</a></li>
        </ul>
      </div>
    </nav>
<?php
    //Find out what page the user wants to see, and display it in the context of the framework laid out here.
    if (isset($_GET['page']))
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
    {?>
<section class="container-fluid">
  <div class="row">
    <header class="col-10 offset-1">
      <h1>The Control Panel</h1>
    </header>
    <div class="col-10 offset-1">
      <p><strong>Welcome to BlogDraw Version 0.0.1 Release Candidate 2!</strong> How can we help you today?  Use the menu above to navigate through the options available to you.</p>
    </div>
  </div>
</section>
<?php
    }
  }
?>
    <!-- jQuery and Bootstrap -->
    <script src="./bootstrap/js/jquery-3.7.1.min.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
