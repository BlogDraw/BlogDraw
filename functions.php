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
require_once ('control/functions/db_connection_handler.php');

if (strpos($_SERVER['REQUEST_URI'], "/control/") !== false) // Only handle auth cookies when an auth attempt is made.
{
  if (!isset($_POST['LoginSubmit']))
    $cookieKey = mb_convert_encoding(htmlspecialchars(bin2hex(random_bytes(256))),"UTF-8");
  if (isset($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']))
  {
    $dBConnection = connect();
    $safeCookie = cleanString($dBConnection,$_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']);
    $dBQuery = "SELECT Cookie FROM `" . DBPREFIX . "_LoginTable` WHERE CHAR_LENGTH(Cookie) > 1;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedCookie = cleanHtmlString($dBConnection, $row['Cookie']);
      if ($returnedCookie == $safeCookie)
        $notLoggedIn = false;
    }
    disconnect($dBConnection);
  }
  if (!isset($_POST['LoginSubmit']) && (!isset($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']) || $notLoggedIn == true))
    setcookie(preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin',$cookieKey,0,'/',URL,FALSE,TRUE); 
}

//HEAD OUTPUT FUNCTIONS
/**
 * This outputs the relevant page title for the <title> tag.
 **/
function output_head_title()
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
      while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      {
        $returnedTitle = cleanHtmlString($dBConnection, $row['Title']);
        echo TITLE . ' | ' . $returnedTitle;
        $isPost = true;
      }
      disconnect($dBConnection);
      if ($isPost == false)
        echo TITLE . ' | ' . $uRI;
  }
}

/**
 * This outputs the site's meta description.
 **/
function output_head_description()
{
  echo DESCRIPTION;
}

/**
 * This outputs the location of the site's current template.
 **/
function output_head_template_location()
{
  echo PROTOCOL . URL . '/template/' . TEMPLATE;
}

//BODY OUTPUT FUNCTIONS
/**
 * This outputs the site's title, with an option to include the description as well.
 * @param hasDescription - Boolean value whether or not to output the description too.
 **/
function output_site_title($hasDescription)
{
  if ($hasDescription)
    echo '<h1>' . TITLE . ' <small>' . DESCRIPTION . '</small></h1>';
  else
    echo '<h1>' . TITLE . '</h1>';
}

/**
 * This outputs contact details for the site, with optional email address and phone number.
 * @param hasEmail - Boolean value whether or not to output the email address too.
 * @param hasPhone - Boolean value whether or not to output the phine number too.
 **/
function output_contact_details($hasEmail,$hasPhone)
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

/**
 * This outputs a link to the website's blog archive page.
 **/
function output_archive_link()
{
  echo PROTOCOL . URL . '/archive';
}

/**
 * This outputs a link to the website's contact page.
 **/
function output_contact_link()
{
  echo PROTOCOL . URL . '/contact';
}

/**
 * This outputs a link to the website's home page.
 **/
function output_home_link()
{
  echo PROTOCOL . URL;
}

/**
 * This outputs the website's template location.
 **/
function output_template_location()
{
  output_head_template_location();
}

/**
 * This outputs the latest non-draft post on the website, by finding it's ID and collating it's data.
 **/
function output_latest_blog_post()
{
  $postID = engine_find_latest_public_post_id();
  engine_collate_post_details($postID);
}

/**
 * This outputs the blog archive page of the website, with options to limit the number of posts that are displayed in full, and to lazy load with a button.
 * @param numberToLoad - The number of blog posts to load.
 * @param lazyLoadIsOK - Boolean value whether or not to lazy load with a button.
 **/
function output_blog_archive($numberToLoad,$lazyLoadIsOK)
{
  $lastPostLoaded = engine_load_blog_archive($numberToLoad);
  if ($lazyLoadIsOK)
  {
    if (!($lastPostLoaded <= $numberToLoad))
      engine_load_blog_archive_button($lastPostLoaded);
  }
  else
    engine_load_blog_archive_alt($lastPostLoaded);
}

/**
 * This outputs the requested page by finding which page has been called, and passing that to the engine that loads the pages.
 **/
function output_canonical_page()
{
  $uRLPath = ltrim($_SERVER['REQUEST_URI'], '/');
  if (strpos($uRLPath,"?fbclid") != FALSE)
    $uRLPath = strstr($uRLPath, "?fbclid", TRUE);
  $elements = explode('/', $uRLPath);
  if (empty($elements[0]) || strcmp("?fbclid", substr($elements[0], 0, 7)) == 0)// No path elements means home
    engine_call_canonical_page('home');
  else if (substr($elements[0],0,4) == "tag-")
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

/**
 * This outputs the author profile, with options to do it bit by bit.
 * @param option - Which optional part of the author profile to load.
 **/
function output_author_profile($option)
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

/**
 * This outputs the template name, from the template code.
 **/
function output_template_name()
{
  $templateData = engine_find_template_data(TEMPLATE);
  echo $templateData["name"];
}

/**
 * This outputs the template author, from the template code.
 **/
function output_template_author()
{
  $templateData = engine_find_template_data(TEMPLATE);
  echo $templateData["author"];
}

/**
 * This outputs the template version, from the template code.
 **/
function output_template_version()
{
  $templateData = engine_find_template_data(TEMPLATE);
  echo $templateData["version"];
}

/**
 * This outputs a link to the template author's contact website, from the template code.
 **/
function output_template_contact_url()
{
  $templateData = engine_find_template_data(TEMPLATE);
  echo $templateData["contactURL"];
}

/**
 * This outputs true if the template has a dark mode and it is set.
 * @return boolean - True if dark mode is enabled.
 **/
function output_template_is_dark_mode()
{
  $templateData = engine_find_template_data(TEMPLATE);
  if(array_key_exists("doDarkMode",$templateData))
    return $templateData["doDarkMode"];
  return false;
}


//ENGINE FUNCTIONS

/**
 * This finds the data provided by the template developer in data.json.
 * @param templateName - The name of the template directory.
 * @return array - The data provided.
 **/
function engine_find_template_data($templateName)
{
  $dataFile = realpath("./template/" . $templateName . "/data.json");
  $data = json_decode(file_get_contents($dataFile), true);
  return $data;
}

/**
 * This handles the data processing for the author profile.
 * @return array - An array of author information needed for the profile.
 **/
function engine_author_profile()
{
  $postID = 1;
  //Check if front page
  $requestedURI= mb_convert_encoding(htmlspecialchars(substr($_SERVER['REQUEST_URI'],1)), "UTF-8");
  if (PROTOCOL . URL . $requestedURI == PROTOCOL.URL || PROTOCOL . URL . "/" . $requestedURI == PROTOCOL.URL."/archive"  || PROTOCOL . URL . "/" . $requestedURI == PROTOCOL.URL."/contact" || substr(PROTOCOL . URL . "/" . $requestedURI,0,(LENGTH+5)) == PROTOCOL.URL."/tag-")
  {// If front page, get author id from latest blog article where not draft
    $postID = engine_find_latest_public_post_id();
    $preamble = "The latest blog author on this site:";
  }
  else
  {// Else find canonical page post link get author id from post where that = nice-title
    $preamble = "Author profile:";
    $dBConnection = connect();
    $requestedURI = cleanHtmlString($dBConnection,$requestedURI);
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $requestedURI . "' ORDER BY ID DESC LIMIT 1;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      $returnedID = cleanString($dBConnection, $row['ID']);
    $postID = $returnedID;
    disconnect($dBConnection);
  }
  $authorID = engine_call_post_field($postID,"AuthorID");
  //find author profile from id
  $dBConnection = connect();
  $dBQuery = "SELECT UserImage,UserBlurb FROM `" . DBPREFIX . "_LoginTable` WHERE ID='" . $authorID . "';";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedAuthorImage = cleanString($dBConnection, $row['UserImage']);
    $returnedAuthorBlurb = cleanHtmlString($dBConnection, $row['UserBlurb']);
  }
  if (!empty($returnedAuthorImage))
    $authorImage = $returnedAuthorImage;
  else
    $authorImage = "X";
  if (!empty($returnedAuthorBlurb))
    $authorBlurb = str_replace("\\\"","\"", $returnedAuthorBlurb);
  else
    $authorBlurb = "X";
  disconnect($dBConnection);
  return array ($authorID,$authorBlurb,$authorImage,$preamble);
}

/**
 * This handles the data processing calling the correct page template and displaying it.
 * @param page - The page template which has been called.
 **/
function engine_call_canonical_page($page)
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

/**
 * This handles the data processing for finding out which post has been called by the user in the URI.
 * It also handles returning engine_call_canonical_page to the 404 page if the post isn't found.
 **/
function engine_find_called_post()
{
  $dBConnection = connect();
  $postCount = 1;
  if (strpos($_SERVER['REQUEST_URI'], "?fbclid") != FALSE)
    $requestedURI = cleanString($dBConnection, strstr(substr($_SERVER['REQUEST_URI'], 1), "?fbclid", TRUE));
  else
    $requestedURI = cleanString($dBConnection, substr($_SERVER['REQUEST_URI'], 1));
  $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $requestedURI . "' ORDER BY ID DESC LIMIT 1;";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedID = cleanString($dBConnection, $row['ID']);
    engine_collate_post_details($returnedID);
    $postCount = 0;
  }
  disconnect($dBConnection);
  if ($postCount == 1)
    engine_call_canonical_page('404');
}

/**
 * This handles the data processing for finding all posts tagged with a specific tag.
 **/
function engine_find_called_tag()
{
  $dBConnection = connect();
  $postCount = 1;
  $requestedTag = cleanString($dBConnection,urldecode(substr($_SERVER['REQUEST_URI'],5)));
  $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND (TagOne='" . $requestedTag . "' OR TagTwo='" . $requestedTag . "' OR TagThree='" . $requestedTag . "') ORDER BY ID DESC;";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedID = cleanString($dBConnection, $row['ID']);
    engine_collate_post_details($returnedID);
    $postCount = 0;
  }
  disconnect($dBConnection);
  if ($postCount == 1)
    engine_call_canonical_page('404');
}

/**
 * This handles creating the optional lazy load button for the blog archive.  It uses Bootstrap 3 classes.
 * @param lastPostLoaded - The ID of the last post to have been loaded.
 **/
function engine_load_blog_archive_button($lastPostLoaded)
{
  ?>
  <form method="post">
    <input id="LastPostLoaded" name="LastPostLoaded" type="hidden" value="<?php echo $lastPostLoaded; ?>" />
    <input type="submit" class="btn btn-primary" name="LoadMore" value="Load More..." />
  </form>
  <?php
}

/**
 * This handles the data processing for loading the desired number of full posts for the blog archive.
 * @param numberToLoad - The number of posts to load.
 * @return returnedID - The post ID of the last post loaded.
 **/
function engine_load_blog_archive($numberToLoad)
{
  if (isset($_POST['LoadMore']) && isset($_POST['LastPostLoaded']))
  {
    $dBConnection = connect();
    $lastOneLoaded = cleanString($dBConnection,$_POST['LastPostLoaded']);
    if (!is_numeric($lastOneLoaded))
      $lastOneLoaded = $numberToLoad + 1;
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND ID<" . $lastOneLoaded . " ORDER BY ID DESC LIMIT " . $numberToLoad . ";";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedID = cleanString($dBConnection, $row['ID']);
      engine_collate_post_details($returnedID);
    }
    disconnect($dBConnection);
  }
  else
  {
    $dBConnection = connect();
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 ORDER BY ID DESC LIMIT " . $numberToLoad . ";";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedID = cleanString($dBConnection, $row['ID']);
      engine_collate_post_details($returnedID);
    }
    disconnect($dBConnection);
  }
  return $returnedID;
}

/**
 * This handles the data processing for loading links to all further blog articles that aren't displayed in full on the blog archive.  A reasonably efficient alternaive to the lazy-loading.
 * @param numberLeft - The number of posts that haven't been fully loaded.
 **/
function engine_load_blog_archive_alt($numberLeft)
{
  if ($numberLeft > 0)
  {
    $dBConnection = connect();
    $dBQuery = "SELECT ID,Title,NiceTitle FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND ID<" . $numberLeft . " ORDER BY ID DESC;";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      echo '<a href="' . URL . htmlspecialchars($row['NiceTitle']) . '" title="' . htmlspecialchars($row['Title']) . '">' . htmlspecialchars($row['Title']) . '</a><br />';
    }
    disconnect($dBConnection);
  }
}

/**
 * This handles the data processing for finding the ID of the latest non-draft post, so it can be loaded.
 * @return returnedID - The ID of the post.
 **/
function engine_find_latest_public_post_id()
{
  $dBConnection = connect();
  $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 ORDER BY ID DESC LIMIT 1;";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    $returnedID = cleanString($dBConnection, $row['ID']);
  disconnect($dBConnection);
  return $returnedID;
}

/**
 * This handles the data processing for collating all data about a post into one HTML <article>.
 * @param postID - The ID of the post.
 **/
function engine_collate_post_details($postID)
{
  echo '<article>';
  echo '<header><h2>' . engine_call_post_field($postID,"Title") . '</h2></header>';
  $postAuthor = engine_call_post_field($postID,"AuthorID");
  $postAuthor = engine_call_author_details($postAuthor);
  $postTagOne = engine_call_post_field($postID,"TagOne");
  $postTagTwo = engine_call_post_field($postID,"TagTwo");
  $postTagThree = engine_call_post_field($postID,"TagThree");
  echo '<p><small>Written by: ' . $postAuthor . ' on: ' . engine_call_post_field($postID,"TimeStamp") . '. 
    Tags: <a href="' . PROTOCOL . URL . '/tag-' . urlencode(strtolower($postTagOne)) . '" title="Visit Tag Archive for: ' . $postTagOne . '">' . $postTagOne . '</a>,
    <a href="' . PROTOCOL . URL . '/tag-' . urlencode(strtolower($postTagTwo)) . '" title="Visit Tag Archive for: ' . $postTagTwo . '">' . $postTagTwo . '</a>,
    <a href="' . PROTOCOL . URL . '/tag-' . urlencode(strtolower($postTagThree)) . '" title="Visit Tag Archive for: ' . $postTagThree . '">' . $postTagThree . '</a>.</small></p>';// Make URLs out of the tags, ensure they're lower-case for consistency.
  echo engine_call_post_field($postID,"Post");
  echo '</article>'; 
}

/**
 * This handles the data processing for collating basic author details into a short caption at the start of a post.
 * @param postAuthor - The ID of the post's author.
 * @return authorCaption - the caption.
 **/
function engine_call_author_details($postAuthor)
{
  $dBConnection = connect();
  $dBQuery = "SELECT DisplayName,Email,EmailIsPublic,URL FROM `" . DBPREFIX . "_LoginTable` WHERE ID='" . $postAuthor . "';";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedDisplayName = cleanString($dBConnection, $row['DisplayName']);
    $returnedEmail = cleanString($dBConnection, $row['Email']);
    $returnedEmailIsPublic = cleanString($dBConnection, $row['EmailIsPublic']);
    $returnedURL = cleanString($dBConnection, $row['URL']);
  }
  disconnect($dBConnection);
  if ($returnedEmailIsPublic == 1 && !empty($returnedURL))
    $authorCaption = '<a href="' . $returnedURL . '" title="Go To ' . $returnedURL . '">' . $returnedDisplayName . '</a> (<a href="mailto:' . $returnedEmail . '" title="Email ' . $returnedEmail . '">Email The Author</a>)';
  else if ($returnedEmailIsPublic == 1 && empty($returnedURL))
    $authorCaption = '<a href="mailto:' . $returnedEmail . '" title="Email ' . $returnedEmail . '">' . $returnedDisplayName . '</a>';
  else if ($returnedEmailIsPublic == 0 && !empty($returnedURL))
    $authorCaption = '<a href="' . $returnedURL . '" title="Go To ' . $returnedURL . '">' . $returnedDisplayName . '</a>';
  else //if ($returnedEmailIsPublic == 0 && empty($returnedURL))
    $authorCaption = $returnedDisplayName;
  return $authorCaption;
}

/**
 * This handles the data procesing required for pulling blog post data from the database.
 * It returns each individual field with a corresponding ID as requested.
 * @param postToCallID - The ID of the post.
 * @param field - The field to get data from.
 * @return returnedField - The data from the requested field.
 **/
function engine_call_post_field($postToCallID,$field)
{
  $dBConnection = connect();
  $dBQuery = "SELECT " . $field . ",PostIsDraft FROM `" . DBPREFIX . "_PostsTable` WHERE ID='" . $postToCallID . "';";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedPostIsDraft = cleanString($dBConnection, $row['PostIsDraft']);
    if ($returnedPostIsDraft == 0)
    {
      if ($field != 'Post')
        $returnedField = cleanString($dBConnection, $row[$field]);
      else
        $returnedField = mb_convert_encoding($row[$field], "UTF-8");
    }
  }
  disconnect($dBConnection);
  return $returnedField;
}
?>
