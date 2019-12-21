<?php
// DO NOT EDIT BELOW THIS LINE!-----------------------------------------
/**
 * Functions.php - this contains most of the core PHP functions that operate BlogDraw.
 * They are split up as follows:
 * - Core Content - this section runs whenever a page calls this script.  It primarily handles security and login sessions.
 * - Head Output Functions - this section contains functions that return outputs which may be needed in the <head> of a template.
 * - Body Output Functions - this section contains functions that return outputs which may be needed in the <body> of a template.
 * - Engine Functions - this section contains functions that parse, operate on, and pass data to and from output functions.
**/
//CORE CONTENT
  global $notLoggedIn;  
  
  require_once ('Back/functions/db_connection_handler.php');
  if (!isset($_POST['LoginSubmit']))
    $cookieKey = mb_convert_encoding(htmlspecialchars(bin2hex(random_bytes(256))),"UTF-8");
  if (isset($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']))
  {
    $dBConnection = connect();
    $safeCookie = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']), "UTF-8"));
    $dBQuery = "SELECT Cookie FROM `" . DBPREFIX . "_LoginTable` WHERE CHAR_LENGTH(Cookie) > 1;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedCookie = mb_convert_encoding($row['Cookie'], "UTF-8");
      if ($returnedCookie == $safeCookie)
        $notLoggedIn = false;
    }
    disconnect($dBConnection);
  }
  if (!isset($_POST['LoginSubmit']) && (!isset($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']) || $notLoggedIn == true))
  {
    setcookie(preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin',$cookieKey,0,'/',URL,FALSE,TRUE);  
  }
  
//HEAD OUTPUT FUNCTIONS  
  function output_head_title() //This function outputs the relevant page title for the <title> tag
  {
    $uRI = ltrim($_SERVER['REQUEST_URI'], '/');
    if ($uRI == NULL)
      echo TITLE;
    else if (substr($uRI,0,4) == "tag-")
      echo TITLE . ' | Tags | ' . urldecode(substr($uRI,4));
    else switch ($uRI)
    {
      case 'archive':
        echo TITLE . ' | Archive';
        break;
      case 'contact':
        echo TITLE . ' | Contact';
        break;
      default:
        $dBConnection = connect();
        $isPost = false;
        $dBQuery = "SELECT Title FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $uRI . "' ORDER BY ID DESC LIMIT 1;";
        $returnQuery = mysqli_query($dBConnection,$dBQuery);
        while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        {
          $returnedTitle = mb_convert_encoding(htmlspecialchars($row['Title']), "UTF-8");
          echo TITLE . ' | ' . $returnedTitle;
          $isPost = true;
        }
        disconnect($dBConnection);
        if($isPost == false)
          echo TITLE . ' | ' . $uRI;
    }
  }
  
  function output_head_description() //This outputs the site's meta description
  {
    echo DESCRIPTION;
  }
  
  function output_head_template_location() //This outputs the location of the site's current template
  {
    echo PROTOCOL . URL . '/template/' . TEMPLATE;
  }
  
//BODY OUTPUT FUNCTIONS
  function output_site_title($hasDescription) //This outputs the site's title, with an option to include the description as well.
  {
    if ($hasDescription)
      echo '<h1>' . TITLE . ' <small>' . DESCRIPTION . '</small></h1>';
    else
      echo '<h1>' . TITLE . '</h1>';
  }
  
  function output_contact_details($hasEmail,$hasPhone) //This outputs contact details for the site, with optional email address and phone number
  {
    if ($hasEmail && $hasPhone)
      echo '<p>You can contact ' . TITLE . ' by email at: <a href="mailto:' . CONTACTEMAIL . '" title="Email ' . TITLE . '">' . CONTACTEMAIL . '</a> or by phone at: <a href="tel:' . CONTACTPHONE . '" title="Phone ' . TITLE . '">' . CONTACTPHONE . '</a>.</p>';
    else if ($hasEmail && !$hasPhone)
      echo '<p>You can contact ' . TITLE . ' by email at: <a href="mailto:' . CONTACTEMAIL . '" title="Email ' . TITLE . '">' . CONTACTEMAIL . '</a>.</p>';
    else if (!$hasEmail && $hasPhone)
      echo '<p>You can contact ' . TITLE . ' by phone at: <a href="tel:' . CONTACTPHONE . '" title="Phone ' . TITLE . '">' . CONTACTPHONE . '</a>.</p>';
    else
      echo '<p>This Website has not set their contact details.</p>';
  }
  
  function output_archive_link() //This outputs a link to the website's blog archive page
  {
    echo PROTOCOL . URL . '/archive';
  }
  
  function output_contact_link() //This outputs a link to the website's contact page
  {
    echo PROTOCOL . URL . '/contact';
  }
  
  function output_home_link() //This outputs a link to the home page
  {
    echo PROTOCOL . URL;
  }

  function output_template_location() //This outputs the website's template location
  {
    output_head_template_location();
  }

  function output_latest_blog_post() //This outputs the latest non-draft post on the website, by finding it's ID and collating it's data.
  {
    $postID = engine_find_latest_public_post_id();
    engine_collate_post_details($postID);
  }
  
  function output_blog_archive($numberToLoad,$lazyLoadIsOK) //This function outputs the blog archive page of the website, with options to limit the number of posts that are displayed in full, and to lazy load with a button.
  {
    $lastPostLoaded = engine_load_blog_archive($numberToLoad);
    if($lazyLoadIsOK)
    {
      if (!($lastPostLoaded <= $numberToLoad))
        engine_load_blog_archive_button($lastPostLoaded);
    }
    else
      engine_load_blog_archive_alt($lastPostLoaded);
  }
  
  function output_canonical_page() //This finds out what page the user has requesed, and passes that to the engine that loads the pages.
  {
    $uRLPath = ltrim($_SERVER['REQUEST_URI'], '/');
    if (strpos($uRLPath,"?fbclid") != FALSE)
      $uRLPath = strstr($uRLPath, "?fbclid", TRUE);
    $elements = explode('/', $uRLPath);
    if(empty($elements[0]))// No path elements means home
      engine_call_canonical_page('home');
    else if(substr($elements[0],0,4) == "tag-")
      engine_call_canonical_page('tag');
    else switch(array_shift($elements))
    {
      case 'archive':
        engine_call_canonical_page('archive');
        break;
      case 'contact':
        engine_call_canonical_page('contact');
        break;
      default:
        engine_call_canonical_page('post');
    }
  }
  
  function output_author_profile($option) //This outputs the author profile, with options to do it bit by bit.
  {
    list($authorID,$authorBlurb,$authorImage,$preamble) = engine_author_profile();
    if ($option =="Preamble")
      echo $preamble;
    else if ($option =="Caption")
      echo engine_call_author_details($authorID);
    else if ($option =="Image")
    {
      if ($authorImage !== 'X')
        echo'<img src="' . $authorImage . '" style="display:block;margin:0 auto;border-radius:50%;max-width:100%;" />';
    }
    else if ($option =="Blurb")
    {
      if ($authorBlurb !== 'X')
        echo  $authorBlurb;
    }
    else //if ($option =="All")
    {
      echo '<aside><header>' . engine_call_author_details($authorID) . '</header>';
      if ($authorImage !== 'X')
        echo'<img src="' . $authorImage . '" style="display:block;margin:0 auto;border-radius:50%;max-width:100%;" />' ;
      if ($authorBlurb !== 'X')
        echo  $authorBlurb;
      echo '</aside>';
    }
  }
  
//ENGINE FUNCTIONS
  function engine_author_profile() //This handles building the author profile
  {
    $postID = 1;
    //Check if front page
    $requestedURI= mb_convert_encoding(htmlspecialchars(substr($_SERVER['REQUEST_URI'],1)), "UTF-8");
    if (PROTOCOL . URL . $requestedURI == PROTOCOL.URL || PROTOCOL . URL . "/" . $requestedURI == PROTOCOL.URL."/archive"  || PROTOCOL . URL . "/" . $requestedURI == PROTOCOL.URL."/contact" || substr(PROTOCOL . URL . "/" . $requestedURI,0,(LENGTH+5)) == PROTOCOL.URL."/tag-")
    {//If front page, get author id from latest blog article where not draft
      $postID = engine_find_latest_public_post_id();
      $preamble = "The latest blog author on this site:";
    }
    else
    {//else find canonical page post link get author id from post where that = nice-title
      $preamble = "Author profile:";
      $dBConnection = connect();
      $requestedURI = mysqli_real_escape_string($dBConnection,$requestedURI);
      $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $requestedURI . "' ORDER BY ID DESC LIMIT 1;";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        $returnedID = mb_convert_encoding(htmlspecialchars($row['ID']), "UTF-8");
      $postID = $returnedID;
      disconnect($dBConnection);
    }
    $authorID = engine_call_post_field($postID,"AuthorID");
    //find author profile from id
    $dBConnection = connect();
    $dBQuery = "SELECT UserImage,UserBlurb FROM `" . DBPREFIX . "_LoginTable` WHERE ID='" . $authorID . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedAuthorImage = mb_convert_encoding(htmlspecialchars($row['UserImage']), "UTF-8");
      $returnedAuthorBlurb = mb_convert_encoding($row['UserBlurb'], "UTF-8");
    }
    if (!empty($returnedAuthorImage)){$authorImage = $returnedAuthorImage;} else {$authorImage = "X";}
    if (!empty($returnedAuthorBlurb)){$authorBlurb = $returnedAuthorBlurb;} else {$authorBlurb = "X";}
    disconnect($dBConnection);
    return array ($authorID,$authorBlurb,$authorImage,$preamble);
  }

  function engine_call_canonical_page($page) //This handles choosing the correct page template for the user and displaying it.
  {
    switch($page)
    {
      case 'home':
        require_once ('./template/' . TEMPLATE . '/home.php');
        break;
      case 'archive':
        require_once ('./template/' . TEMPLATE . '/archive.php');
        break;
      case 'contact':
        require_once ('./template/' . TEMPLATE . '/contact.php');
        break;
      case 'tag':
        engine_find_called_tag();
        break;
      case 'post':
        engine_find_called_post();
        break;
      case '404':
        echo 'Error 404: Page not found.  Better luck next time';
        break;
      default:
        echo 'Page content error.';
    }
  }

  function engine_find_called_post() //This handles finding out which post has been called by the user in the URI, also handles returning engine_call_canonical_page to the 404 page if the post isn't found.
  {
    $dBConnection = connect();
    $postCount = 1;
    if (strpos($_SERVER['REQUEST_URI'], "?fbclid") != FALSE)
      $requestedURI = mysqli_real_escape_string($dBConnection, mb_convert_encoding(htmlspecialchars(strstr(substr($_SERVER['REQUEST_URI'], 1), "?fbclid", TRUE)), "UTF-8"));
    else
      $requestedURI = mysqli_real_escape_string($dBConnection, mb_convert_encoding(htmlspecialchars(substr($_SERVER['REQUEST_URI'], 1)), "UTF-8"));
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $requestedURI . "' ORDER BY ID DESC LIMIT 1;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedID = mb_convert_encoding(htmlspecialchars($row['ID']), "UTF-8");
      engine_collate_post_details($returnedID);
      $postCount = 0;
    }
    disconnect($dBConnection);
    if ($postCount == 1)
      engine_call_canonical_page('404');
  }
  
  function engine_find_called_tag() //This handles finding all posts tagged with a specific tag
  {
    $dBConnection = connect();
    $postCount = 1;
    $requestedTag= mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars(urldecode(substr($_SERVER['REQUEST_URI'],5))), "UTF-8"));
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND (TagOne='" . $requestedTag . "' OR TagTwo='" . $requestedTag . "' OR TagThree='" . $requestedTag . "') ORDER BY ID DESC;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedID = mb_convert_encoding(htmlspecialchars($row['ID']), "UTF-8");
      engine_collate_post_details($returnedID);
      $postCount = 0;
    }
    disconnect($dBConnection);
    if ($postCount == 1)
      engine_call_canonical_page('404');
  }

  function engine_load_blog_archive_button($lastPostLoaded) //This handles creating the optional lazy load button for the blog archive.  It uses Bootstrap 3 classes.
  {
    ?>
    <form method="post">
      <input id="LastPostLoaded" name="LastPostLoaded" type="hidden" value="<?php echo $lastPostLoaded; ?>" />
      <input type="submit" class="btn btn-default" name="LoadMore" value="Load More..." />
    </form>
    <?php
  }

  function engine_load_blog_archive($numberToLoad) //This handles loading the desired number of full posts for the blog archive.
  {
    if(isset($_POST['LoadMore']) && isset($_POST['LastPostLoaded']))
    {
      $dBConnection = connect();
      $lastOneLoaded = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['LastPostLoaded']), "UTF-8"));
      if (!is_numeric($lastOneLoaded))
        $lastOneLoaded = $numberToLoad + 1;
      $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND ID<" . $lastOneLoaded . " ORDER BY ID DESC LIMIT " . $numberToLoad . ";";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      {
        $returnedID = mb_convert_encoding(htmlspecialchars($row['ID']), "UTF-8");
        engine_collate_post_details($returnedID);
      }
      disconnect($dBConnection);
    }
    else
    {
      $dBConnection = connect();
      $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 ORDER BY ID DESC LIMIT " . $numberToLoad . ";";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      {
        $returnedID = mb_convert_encoding(htmlspecialchars($row['ID']), "UTF-8");
        engine_collate_post_details($returnedID);
      }
      disconnect($dBConnection);
    }
    return $returnedID;
  }
  function engine_load_blog_archive_alt($numberLeft) //This loads links to all further blog articles that aren't displayed in full on the blog archive.  A reasonably efficient alternaive to the lazy-loading.
  {
    if($numberLeft > 0)
    {
      $dBConnection = connect();
      $dBQuery = "SELECT ID,Title,NiceTitle FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND ID<" . $numberLeft . " ORDER BY ID DESC;";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      {
        echo '<a href="' . URL . htmlspecialchars($row['NiceTitle']) . '" title="' . htmlspecialchars($row['Title']) . '">' . htmlspecialchars($row['Title']) . '</a><br />';
      }
      disconnect($dBConnection);
    }
  }

  function engine_find_latest_public_post_id() //This finds the ID of the latest non-draft post, so it can be loaded up.
  {
    $dBConnection = connect();
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 ORDER BY ID DESC LIMIT 1;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      $returnedID = mb_convert_encoding(htmlspecialchars($row['ID']), "UTF-8");
    disconnect($dBConnection);
    return $returnedID;
  }

  function engine_collate_post_details($postID) //this collates all data about a post into one html <article>.
  {
    echo '<article>';
    echo '<header><h2>' . engine_call_post_field($postID,"Title") . '</h2></header>';
    $postAuthor = engine_call_post_field($postID,"AuthorID");
    $postAuthor = engine_call_author_details($postAuthor);
    $postTagOne = engine_call_post_field($postID,"TagOne");
    $postTagTwo = engine_call_post_field($postID,"TagTwo");
    $postTagThree = engine_call_post_field($postID,"TagThree");
    echo '<p><small>Written by: ' . $postAuthor . ' on: ' . engine_call_post_field($postID,"TimeStamp") . '.  
      Tags: <a href="' . PROTOCOL . URL . '/tag-' . urlencode($postTagOne) . '" title="Visit Tag Archive for: ' . $postTagOne . '">' . $postTagOne . '</a>, 
      <a href="' . PROTOCOL . URL . '/tag-' . urlencode($postTagTwo) . '" title="Visit Tag Archive for: ' . $postTagTwo . '">' . $postTagTwo . '</a>, 
      <a href="' . PROTOCOL . URL . '/tag-' . urlencode($postTagThree) . '" title="Visit Tag Archive for: ' . $postTagThree . '">' . $postTagThree . '</a>.</small></p>';
    echo engine_call_post_field($postID,"Post");
    echo '</article>';  
  }

  function engine_call_author_details($postAuthor) //This finds basic author details and collates them into a short caption at the start of a post.
  {
    $dBConnection = connect();
    $dBQuery = "SELECT Username,Email,EmailIsPublic,URL FROM `" . DBPREFIX . "_LoginTable` WHERE ID='" . $postAuthor . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedUsername = mb_convert_encoding(htmlspecialchars($row['Username']), "UTF-8");
      $returnedEmail = mb_convert_encoding(htmlspecialchars($row['Email']), "UTF-8");
      $returnedEmailIsPublic = mb_convert_encoding(htmlspecialchars($row['EmailIsPublic']), "UTF-8");
      $returnedURL = mb_convert_encoding(htmlspecialchars($row['URL']), "UTF-8");
    }
    disconnect($dBConnection);
    if($returnedEmailIsPublic == 1 && !empty($returnedURL))
      $authorCaption = '<a href="' . $returnedURL . '" title="Go To ' . $returnedURL . '">' . $returnedUsername . '</a>(<a href="mailto:' . $returnedEmail . '" title="Email ' . $returnedEmail . '">Email The Author</a>)';
    else if($returnedEmailIsPublic == 1 && empty($returnedURL))
      $authorCaption = '<a href="mailto:' . $returnedEmail . '" title="Email ' . $returnedEmail . '">' . $returnedUsername . '</a>';
    else if($returnedEmailIsPublic == 0 && !empty($returnedURL))
      $authorCaption = '<a href="' . $returnedURL . '" title="Go To ' . $returnedURL . '">' . $returnedUsername . '</a>';
    else //if($returnedEmailIsPublic == 0 && empty($returnedURL))
      $authorCaption = $returnedUsername;
    return $authorCaption;
  }

  function engine_call_post_field($postToCallID,$field) //This handles the majority of pulling blog post data from the database.  It returns each individual field with a corresponding ID as requested.
  {
    $dBConnection = connect();
    $dBQuery = "SELECT " . $field . ",PostIsDraft FROM `" . DBPREFIX . "_PostsTable` WHERE ID='" . $postToCallID . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedPostIsDraft = mb_convert_encoding(htmlspecialchars($row['PostIsDraft']), "UTF-8");
      if ($returnedPostIsDraft == 0)
      {
        if ($field != 'Post')
          $returnedField = mb_convert_encoding(htmlspecialchars($row[$field]), "UTF-8");
        else
          $returnedField = mb_convert_encoding($row[$field], "UTF-8");
      }
    }
    disconnect($dBConnection);
    return $returnedField;
  }
?>