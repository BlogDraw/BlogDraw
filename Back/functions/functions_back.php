<?php
require_once ('db_connection_handler.php');
/**
 * functions_back.php - this contains most of the core PHP functions that operate the back end of BlogDraw (Known as "The Back").
 * They are split up as follows:
 * - Functions named engine_... - these contain the code that runs each page or aspect of a page - the complex algorithms.
 * - Functions named UI_... - these contain the code for the User Interfaces (UIs) of each page.  We need to keep these in PHP instead of HTML as many of them need dynamically generated content.
 * - Functions named sub_... - these contain extra logic needed for the function they're relevant to, but need their own function for readability, portability, memory management, etc...
**/
  function engine_account_page($safeCookie) //This handles the data for the Account page.
  {
    $dBConnection = connect();
    $dBQuery = "SELECT Username,Email,UserImage,UserBlurb,Company,URL,EmailIsPublic FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedUsername = mb_convert_encoding($row['Username'], "UTF-8");
      $returnedEmail = mb_convert_encoding($row['Email'], "UTF-8");
      $returnedUserImage = mb_convert_encoding($row['UserImage'], "UTF-8");
      $returnedUserBlurb = mb_convert_encoding($row['UserBlurb'], "UTF-8");
      $returnedCompany = mb_convert_encoding($row['Company'], "UTF-8");
      $returnedURL = mb_convert_encoding($row['URL'], "UTF-8");
      $returnedEmailIsPublic = mb_convert_encoding($row['EmailIsPublic'], "UTF-8");
    }
    disconnect($dBConnection);

    if (isset($_POST['AccountSubmit']))
    {
      $dBConnection = connect();
      if(isset($_POST['Username']) && isset($_POST['Email']) && !empty($_POST['Username']) && !empty($_POST['Email']))
      {
        $safeUsername = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Username']),"UTF-8"));
        $safeEmail = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Email']),"UTF-8"));
        if ($safeUsername != $returnedUsername)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Username = '" . $safeUsername . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedUsername = $safeUsername;
        }
        if ($safeEmail != $returnedEmail)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Email = '" . $safeEmail . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedEmail = $safeEmail;
        }
      }
      else
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>You need at least username and email address for an account.</strong></p></div>';
    
      if(isset($_POST['Password1']) && isset($_POST['Password2']) && !empty($_POST['Password1']))
      {
        if ($_POST['Password1'] == $_POST['Password2'])
        {
          $newPassword = password_hash($_POST['Password1'], PASSWORD_DEFAULT);
          $safePassword = mysqli_real_escape_string($dBConnection,$newPassword);
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Password = '" . $safePassword . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Your account password has been reset, please look after it.</strong></p></div>';
        }
        else
          echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Passords don&#39;t match!</strong></p></div>';
      }
      if(isset($_POST['Company']))
      {
        $safeCompany = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Company']),"UTF-8"));
        if ($safeCompany != $returnedCompany)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Company = '" . $safeCompany . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedCompany = $safeCompany;
        }
      }
      if(isset($_POST['UserURL']))
      {
        $safeURL = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['UserURL']),"UTF-8"));
        if ($safeURL != $returnedURL)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET URL = '" . $safeURL . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedURL = $safeURL;
        }
      }
      if(isset($_POST['UserImage']))
      {
        $safeUserImage = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['UserImage']),"UTF-8"));
        if ($safeUserImage != $returnedUserImage)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET UserImage = '" . $safeUserImage . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedUserImage = $safeUserImage;
        }
      }
      if(isset($_POST['UserBlurb']))
      {
        $safeUserBlurb = mysqli_real_escape_string($dBConnection,mb_convert_encoding($_POST['UserBlurb'],"UTF-8"));
        if ($safeUserBlurb != $returnedUserBlurb)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET UserBlurb = '" . $safeUserBlurb . "' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedUserBlurb = $safeUserBlurb;
        }
      }
      if(isset($_POST['EmailPublic']))
      {
        if ($returnedEmailIsPublic == 0)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET EmailIsPublic = '1' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedEmailIsPublic = 1;
        }
      }
      else if(!isset($_POST['EmailPublic']))
      {
        if ($returnedEmailIsPublic == 1)
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET EmailIsPublic = '0' WHERE Cookie = '" . $safeCookie . "';";
          mysqli_query($dBConnection,$dBQuery);
          $returnedEmailIsPublic = 0;
        }
      }
      disconnect($dBConnection);
    }
    //Call in the UI, and pass variables to autofill the form
    UI_account_page($returnedUsername,$returnedCompany,$returnedURL,$returnedEmail,$returnedUserBlurb,$returnedUserImage,$returnedEmailIsPublic);
  }

  function UI_account_page($returnedUsername,$returnedCompany,$returnedURL,$returnedEmail,$returnedUserBlurb,$returnedUserImage,$returnedEmailIsPublic) //This handles the UI for the Account page.
  {
  ?><div class="container-fluid">
  <div class="row">
    <form method="post" id="AccountChangeForm" class="form-horizontal col-xs-10 col-xs-push-1">
      <fieldset class="form-group">
        <legend>My Account:</legend>
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Username">Username:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Username" id="Username" value="  <?php echo $returnedUsername; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Password1">Change Password:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="password" class="form-control" name="Password1" id="Password1" />
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-9 col-sm-push-3">
            <input type="password" class="form-control" name="Password2" id="Password2" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Company">Company:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Company" id="Company" value="  <?php echo $returnedCompany; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="UserURL">Website:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="UserURL" id="UserURL" value="  <?php echo $returnedURL; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Email">Email:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Email" id="Email" value="  <?php echo $returnedEmail; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="UserImage">User Photo URL:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="UserImage" id="UserImage" value="  <?php echo $returnedUserImage; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="UserBlurb">Your User Blurb (accepts HTML):</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="UserBlurb" id="UserBlurb" value="  <?php echo $returnedUserBlurb; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="EmailPublic">Make email address public:</label> 
          <div class="col-xs-12 col-sm-9">
            <div class="checkbox">
                <?php if ($returnedEmailIsPublic == 1){ ?>
              <input type="checkbox" name="EmailPublic" id="EmailPublic" checked />
                <?php }else{ ?>
              <input type="checkbox" name="EmailPublic" id="EmailPublic" />
                <?php } ?>
            </div>
          </div>
        </div>
        <br />
        <div class="row">
          <input type="submit" class="btn btn-default col-xs-3" name="AccountSubmit" value="Change" />
        </div>
      </fieldset>
    </form>
  </div>
</div><?php
  }
  
  function engine_login_page($safeCookie) //This handles the data for the Login page.
  {
    if (isset($_POST['LoginSubmit']))
    {
      $dBConnection = connect();
      $dBQuery = "SELECT ID,Username,Password FROM `" . DBPREFIX . "_LoginTable`;";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      $safeUsername = mysqli_real_escape_string($dBConnection,mb_convert_encoding($_POST['Username'],"UTF-8"));
      $safePassword = mysqli_real_escape_string($dBConnection,mb_convert_encoding($_POST['Password'],"UTF-8"));
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      {
        $returnedUsername = mb_convert_encoding($row['Username'], "UTF-8");
        $returnedPassword = mb_convert_encoding($row['Password'], "UTF-8");
        $returnedID = mb_convert_encoding($row['ID'], "UTF-8");
        if ($returnedUsername == $safeUsername)
        {
          if (password_verify($safePassword,$returnedPassword))
          {
            $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Cookie = '" . $safeCookie . "' WHERE ID = '" . $returnedID . "';";
            mysqli_query($dBConnection,$dBQuery);
            echo '<script>window.location.href = "' . PROTOCOL . URL . '/Back/";</script>';
            return;
          }
        }
      }
      echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Username and/or Password is Invalid.</strong></p></div>';
      disconnect($dBConnection);
    }
    UI_login_page();
  }
  
  function UI_login_page() //This handles the UI for the Login page.
  {
  ?><div class="container-fluid">
  <div class="row">
    <form method="post" id="LoginForm" class="form-horizontal col-xs-10 col-xs-push-1">
      <fieldset class="form-group">
        <legend>Login:</legend>
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Username">Username:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Username" id="Username" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Password">Password:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="password" class="form-control" name="Password" id="Password" />
          </div>
        </div>
        <br />
        <div class="row">
          <input type="submit" class="btn btn-default col-xs-3" name="LoginSubmit" value="Login" />
        </div>
      </fieldset>
    </form>
  </div>
</div>
<?php
  }
  
  function engine_add_posts_page($safeCookie) //This handles the data for the "Add Posts" page.
  {
    if (isset($_POST['PostSubmit']))
    {
      if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
      else
        sub_engine_add_posts_SubmitOrDraft('Submit',$safeCookie);
    }
    else if (isset($_POST['PostDraft']))
    {
      if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
      else
        sub_engine_add_posts_SubmitOrDraft('Draft',$safeCookie);
    }
    UI_add_edit_posts_page('Add',$safeCookie,0);
  }
  
  function sub_engine_add_posts_SubmitOrDraft($submitOrDraft,$safeCookie) //This handles the Submit or Draft buttons present on the "Add Posts" page.
  {
    $dBConnection = connect();
    $safeTitle = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Title']),"UTF-8"));
    $safeNiceTitle = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $safeTitle)));
    $safePost = mysqli_real_escape_string($dBConnection,mb_convert_encoding('<div>' . nl2br($_POST['Content'],true) . '</div>',"UTF-8"));
    $safeTagList = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Tags']),"UTF-8"));
    $safeTagArray = explode(',', $safeTagList);
    $safeTagOne = $safeTagArray[0];
    $safeTagTwo = $safeTagArray[1];
    $safeTagThree = $safeTagArray[2];
  
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      $returnedAuthorID = mb_convert_encoding($row['ID'], "UTF-8");
    if ($submitOrDraft == 'Submit')
    {
      $dBQuery = "INSERT INTO `" . DBPREFIX . "_PostsTable` (AuthorID,Title,NiceTitle,TagOne,TagTwo,TagThree,Post,PostIsDraft) VALUES ('" . $returnedAuthorID . "','" . $safeTitle . "','" . $safeNiceTitle . "','" . $safeTagOne . "','" . $safeTagTwo . "','" . $safeTagThree . "','" . $safePost . "',0);";
      mysqli_query($dBConnection,$dBQuery);
      echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Posted!</strong></p></div>';
    }
    else if ($submitOrDraft == 'Draft')
    {
      $dBQuery = "INSERT INTO `" . DBPREFIX . "_PostsTable` (AuthorID,Title,NiceTitle,TagOne,TagTwo,TagThree,Post,PostIsDraft) VALUES ('" . $returnedAuthorID . "','" . $safeTitle . "','" . $safeNiceTitle . "','" . $safeTagOne . "','" . $safeTagTwo . "','" . $safeTagThree . "','" . $safePost . "',1);";
      mysqli_query($dBConnection,$dBQuery);
      echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Saved!</strong></p></div>';
    }
    disconnect($dBConnection);
  }
  
  function sub_UI_add_edit_posts_FindAuthorDetails ($safeCookie) //This handles the filling in the author details for the UI for the "Add or Edit Posts" page.
  {
    $dBConnection = connect();
    $dBQuery = "SELECT Username FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      $returnedAuthor = mb_convert_encoding($row['Username'], "UTF-8");
    disconnect($dBConnection);
    return '<p>Written by: ' . $returnedAuthor . ' on: ' . date("Y-m-d") . '.</p>';
  }
  
  function UI_add_edit_posts_page($addEdit,$safeCookie,$editPostID) //This handles the UI for the "Add or Edit Posts" page.
  {
  ?><script>
function controlBoldFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ "<strong> BOLD TEXT HERE </strong>" +PostText );
}
      
function controlItalicFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ "<em> ITALIC TEXT HERE </em>" +PostText );
}
      
function controlUnderlineFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ '<span style="text-decoration:underline;"> UNDERLINED TEXT HERE </span>' +PostText );
}
      
function controlQuoteFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ '<blockquote> QUOTE HERE </blockquote>' +PostText );
}
      
function controlCodeFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ '<code> CODE HERE </code>' +PostText );
}
</script>
<div class="container-fluid">
  <div class="row">
    <form method="post" id="AccountChangeForm" class="form-horizontal col-xs-10 col-xs-push-1">
      <fieldset class="form-group">
          <?php if ($addEdit == 'Add'){ ?>
        <legend>Add a Post:</legend>
          <?php } else if ($addEdit == 'Edit'){ ?>
        <legend>Edit a Post:</legend>
          <?php } ?>
        <div class="row"> 
          <div class="col-xs-12">
            <input type="text" class="form-control" name="Title" id="Title" placeholder="Title" />
          </div>
        </div>
        <div class="row"> 
          <div class="col-xs-12">
              <?php echo sub_UI_add_edit_posts_FindAuthorDetails ($safeCookie); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-md-8">
            <fieldset>
              <div>
                <a class="btn btn-default btn-sm" name="controlBold" onclick="controlBoldFunc()">B</a>
                <a class="btn btn-default btn-sm" name="controlItalic" onclick="controlItalicFunc()">I</a>
                <a class="btn btn-default btn-sm" name="controlUnderline" onclick="controlUnderlineFunc()">U</a>
                <a class="btn btn-default btn-sm" name="controlQuote" onclick="controlQuoteFunc()">"</a>
                <a class="btn btn-default btn-sm" name="controlCode" onclick="controlCodeFunc()">&lt;&gt;</a>
              </div>
              <div style="height:0.4rem;"></div>
              <div>
                <textarea class="form-control" rows="12" name="Content" id="Content" ></textarea>
              </div>
              <br />
            </fieldset>
          </div>
          <div class="col-xs-12 col-md-4">
            <div class="form-control" style="height:315px;overflow-y:scroll;">  <?php engine_media_plugin(); ?></div>
          </div>
        </div>
        <br />
        <div class="row"> 
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="Tags" id="Tags" placeholder="3 comma separated Tags I.E: blog,post,hello" />
          </div>
        </div>
        <br />
          <?php if ($addEdit == 'Edit'){ ?><input type="hidden" name="Editor" id="Editor" value="  <?php echo $editPostID; ?>" />   <?php } ?>
        <div class="btn-group col-xs-12">
          <input type="submit" class="btn btn-default col-xs-4" name="PostSubmit" value="Write Post" />
          <input type="submit" class="btn btn-default col-xs-4" name="PostDraft" value="Save Draft Post" />
          <input type="submit" class="btn btn-default col-xs-4" name="PostCancel" value="Cancel Post" />
        </div>
      </fieldset>
    </form>
  </div>
</div><?php
  }
  
  function engine_edit_posts_page($safeCookie) //This handles the data for the "Edit Posts" page.
  {
    if (isset($_POST['EditSubmit']) && isset($_POST['Edit']) && !empty($_POST['Edit']))
    {
      $dBConnection = connect();
      $safeEditPostNo = mysqli_real_escape_string($dBConnection,mb_convert_encoding($_POST['Edit'],"UTF-8"));
      $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        $returnedID = mb_convert_encoding($row['ID'], "UTF-8");
      $dBQuery = "SELECT ID,AuthorID,Title,Post,TagOne,TagTwo,TagThree FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $safeEditPostNo . "';";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      {
        $returnedPostID = mb_convert_encoding($row['ID'], "UTF-8");
        $returnedAuthorID = mb_convert_encoding($row['AuthorID'], "UTF-8");
        $returnedTitle = mb_convert_encoding($row['Title'], "UTF-8");
        $returnedPost = mb_convert_encoding($row['Post'], "UTF-8");
        $returnedPost = str_replace("<br />", "", $returnedPost); //Writing a post adds in HTML linebreaks.  We want to remove these so we don't add them twice.
        $returnedTagOne = mb_convert_encoding($row['TagOne'], "UTF-8");
        $returnedTagTwo = mb_convert_encoding($row['TagTwo'], "UTF-8");
        $returnedTagThree = mb_convert_encoding($row['TagThree'], "UTF-8");
        $returnedTags = $returnedTagOne . ',' . $returnedTagTwo . ',' . $returnedTagThree;
      }
      if ($returnedID == $returnedAuthorID)
      {
        UI_add_edit_posts_page('Edit',$safeCookies,$returnedPostID);
        sub_UI_add_edit_posts_JSFillForEdit($returnedPostID,$returnedTitle,$returnedPost,$returnedTags);
      }
      disconnect($dBConnection);
    }
    else if (isset($_POST['DeleteSubmit']) && isset($_POST['Delete']) && !empty($_POST['Delete']))
    {
      $dBConnection = connect();
      $safeDeletePostNo = mysqli_real_escape_string($dBConnection,mb_convert_encoding($_POST['Delete'],"UTF-8"));
      $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        $returnedID = mb_convert_encoding($row['ID'], "UTF-8");
      $dBQuery = "SELECT AuthorID FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $safeDeletePostNo . "';";
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        $returnedAuthorID = mb_convert_encoding($row['AuthorID'], "UTF-8");
      if ($returnedID == $returnedAuthorID)
      {
        $dBQuery = "DELETE FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $safeDeletePostNo . "';";
        mysqli_query($dBConnection,$dBQuery);
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Your post has been deleted.</strong></p></div>';
      }
      disconnect($dBConnection);
    }
    else
      UI_edit_posts_page($safeCookie);
    
    //Handle the add or edit UI
    if (isset($_POST['PostSubmit']))
    {
      if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
      else
        sub_engine_edit_posts_SubmitOrDraft('Submit',$safeCookie);
    }
    else if (isset($_POST['PostDraft']))
    {
      if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
      else
        sub_engine_edit_posts_SubmitOrDraft('Draft',$safeCookie);
    }
  }
  
  function sub_engine_edit_posts_SubmitOrDraft($submitOrDraft,$safeCookie) //This handles the Submit or Draft buttons present on the "Edit Posts" page.
  {
    $dBConnection = connect();
    $safeTitle = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Title']),"UTF-8"));
    $safeNiceTitle = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $safeTitle)));
    $safePost = mysqli_real_escape_string($dBConnection,mb_convert_encoding('<div>' . nl2br($_POST['Content'],true) . '</div>',"UTF-8"));
    $safeTagList = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Tags']),"UTF-8"));
    $safeEditID = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Editor']),"UTF-8"));
    $safeTagArray = explode(',', $safeTagList);
    $safeTagOne = $safeTagArray[0];
    $safeTagTwo = $safeTagArray[1];
    $safeTagThree = $safeTagArray[2];
  
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      $returnedID = mb_convert_encoding($row['ID'], "UTF-8");
    if ($submitOrDraft == 'Submit')
    {
      $dBQuery = "SELECT AuthorID FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $safeEditID . "';";
      mysqli_query($dBConnection,$dBQuery);
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        $returnedAuthorID = mb_convert_encoding($row['AuthorID'], "UTF-8");
      if ($returnedID == $returnedAuthorID)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_PostsTable` SET Title = '" . $safeTitle . "',NiceTitle = '" . $safeNiceTitle . "',TagOne = '" . $safeTagOne . "',TagTwo = '" . $safeTagTwo . "',TagThree = '" . $safeTagThree . "',Post = '" . $safePost . "',PostIsDraft = 0 WHERE ID = '" . $safeEditID . "';";
        mysqli_query($dBConnection,$dBQuery);
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Posted!</strong></p></div>';
      }
    }
    else if ($submitOrDraft == 'Draft')
    {
      $dBQuery = "SELECT AuthorID FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $safeEditID . "';";
      mysqli_query($dBConnection,$dBQuery);
      $returnQuery = mysqli_query($dBConnection,$dBQuery);
      while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
        $returnedAuthorID = mb_convert_encoding($row['AuthorID'], "UTF-8");
      if ($returnedID == $returnedAuthorID)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_PostsTable` SET Title = '" . $safeTitle . "',NiceTitle = '" . $safeNiceTitle . "',TagOne = '" . $safeTagOne . "',TagTwo = '" . $safeTagTwo . "',TagThree = '" . $safeTagThree . "',Post = '" . $safePost . "',PostIsDraft = 1 WHERE ID = '" . $safeEditID . "';";
        mysqli_query($dBConnection,$dBQuery);
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Saved!</strong></p></div>';
      }
    }
    disconnect($dBConnection);
  }
  
  function sub_UI_add_edit_posts_JSFillForEdit($returnedPostID,$returnedTitle,$returnedPost,$returnedTags) //This fills in the UI form on the "Add or Edit Posts" page if needed.
  {
  ?><script>
    document.getElementById("Title").value = "  <?php echo $returnedTitle; ?>";
    document.getElementById("Content").value = `  <?php echo substr($returnedPost,5,-6); ?>`;
    document.getElementById("Tags").value = "  <?php echo $returnedTags; ?>";
  </script><?php
  }
  
  function UI_edit_posts_page($safeCookie) //This handles the UI for the "Edit Posts" page.
  {?><div class="container-fluid">
  <div class="row">
    <form class="form-horizontal col-xs-10 col-xs-push-1">
      <fieldset class="form-group">
        <legend>View and Edit Posts</legend>
      </fieldset>
    </form>
  </div>
  <div class="row">
    <div class="col-xs-10 col-xs-push-1">
      <div class="table-responsive">
        <table class="table table-condensed">
          <thead>
            <tr>
              <th>Number:</th>
              <th>Title:</th>
              <th>Content:</th>
              <th>Written On:</th>
              <th>Actions:</th>
            </tr>
          </thead>
          <tbody>
              <?php sub_UI_edit_posts_TableContent($safeCookie); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div><?php
  }
  
  function sub_UI_edit_posts_TableContent($safeCookie) //This fills in the table on the "Edit Posts" page.
  {
    $dBConnection = connect();
    $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
      $returnedID = mb_convert_encoding($row['ID'], "UTF-8");
    $dBQuery = "SELECT ID,Title,Post,Timestamp,PostIsDraft FROM `" . DBPREFIX . "_PostsTable` WHERE AuthorID = '" . $returnedID . "';";
    $returnQuery = mysqli_query($dBConnection,$dBQuery);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedPostID = mb_convert_encoding($row['ID'], "UTF-8");
      $returnedTitle = mb_convert_encoding($row['Title'], "UTF-8");
      $returnedPost = substr(strip_tags(mb_convert_encoding($row['Post'], "UTF-8")),0,80);
      $returnedTimestamp = mb_convert_encoding($row['Timestamp'], "UTF-8");
      $returnedPostIsDraft = mb_convert_encoding($row['PostIsDraft'], "UTF-8");
      if ($returnedPostIsDraft == 1)
        $returnedPost = substr("[DRAFT]: " . $returnedPost,0,80);
      echo'<tr><td>' . $returnedPostID . '</td><td>' . $returnedTitle . '</td><td>' . $returnedPost . '</td><td>' . $returnedTimestamp . '</td><td><form method="post" style="display:inline;"><input id="Edit" name="Edit" type="hidden" value="' . $returnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="EditSubmit" value="Edit" /></form>&nbsp;<form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $returnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td></tr>';
    }
    disconnect($dBConnection);
  }
    
  function engine_media_page() //This handles the data for the Media page.
  {
    if (isset($_POST['DeleteSubmit']) && isset($_POST['Delete']) && !empty($_POST['Delete']))
    {
      $file = htmlspecialchars($_POST['Delete']);
      if (file_exists($file) && substr($file,0,11) == "../Uploads/")      
        unlink($file) or die("Couldn't delete file");
    }
    
    if (isset($_POST['AddSubmit']))
    {
      $targetDir = "../Uploads/";
      $targetFile = $targetDir . basename($_FILES["UploadFile"]["name"]);
      $fineToUpload = 1;
      $fileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

      if (file_exists($targetFile) && $targetFile != $targetDir . "favicon.ico" && $targetFile != $targetDir . "apple-touch-icon.png") //Check if file already exists
      {
        echo "File already exists.";
        $fineToUpload = 0;
      }

      if ($_FILES["UploadFile"]["size"] > 2000000) //Check file size
      {
        echo "Your file is too large.";
        $fineToUpload = 0;
      }

      if(!($fileType == "jpg" || $fileType == "jpeg" || $fileType == "png" || $fileType == "bmp" || $fileType == "gif" || $fileType == "tiff" || $fileType == "ogg" || $fileType == "ogv" || $fileType == "webm" || $fileType == "mp4" || $fileType == "txt" || $fileType == "rtf" || $fileType == "pdf" || $fileType == "docx" || $fileType == "pptx" || $fileType == "xlsx" || $fileType == "csv" || $fileType == "odt" || $fileType == "odp" || $fileType == "ods" || $fileType == "odg" || $fileType == "mp3" || $fileType == "ico")) //Allow certain file formats
      {
        echo "Allowed formats are: jpg, jpeg, png, bmp, gif, tiff, ogg, ogv, webm, mp4, mp3, txt, rtf, pdf, docx, pptx, xlsx, csv, odt, odp, ods, odg, ico.";
        $fineToUpload = 0;
      }

      if ($fineToUpload == 0) //Check if $fineToUpload is set to 0 by an error
        echo "Sorry, your file was not uploaded.";
      else //if everything is ok, try to upload file
      {
        if ($targetFile == $targetDir . "favicon.ico")
        {
          if (file_exists("../Uploads/favicon.ico"))       
            unlink("../Uploads/favicon.ico") or die("Couldn't delete old file.");
        }
        else if ($targetFile == $targetDir . "apple-touch-icon.png")
        {
          if (file_exists("../Uploads/apple-touch-icon.png"))      
            unlink("../Uploads/apple-touch-icon.png") or die("Couldn't delete old file.");
        }
        
        if (move_uploaded_file($_FILES["UploadFile"]["tmp_name"], $targetFile))
          echo "The file ". basename( $_FILES["UploadFile"]["name"]). " has been uploaded.";
        else
          echo "Sorry, there was an error uploading your file.";
      }
    }
    UI_media_page('Page');
  }
  
  function UI_media_page($pageOrPlugin) //This handles the UI for the Media page, and it's plugin on the "Add or Edit Posts" page.
  {
    if ($pageOrPlugin != 'Plugin'){ ?><div class="container-fluid">
  <div class="row">
    <form class="form-horizontal col-xs-10 col-xs-push-1">
      <fieldset class="form-group">
        <legend>Your Media:</legend>
      </fieldset>
    </form>
  </div>
  <div class="row">  <?php } ?>
      <?php if ($pageOrPlugin != 'Plugin'){ ?><div class="col-xs-10 col-xs-push-1">  <?php } else { ?><div class="col-xs-12">  <?php } ?>
      <div class="table-responsive">
        <table class="table table-condensed">
          <thead>
            <tr>
              <th>Image:</th>
              <th>HTML Code (copy into a blog post):</th>
              <th>Location:</th>
              <th>Uploaded on:</th>
                <?php if ($pageOrPlugin != 'Plugin'){ ?><th>Delete:</th>  <?php } ?>
            </tr>
          </thead>
          <tbody>
              <?php sub_UI_media_page_FindAndPrintFileData($pageOrPlugin); ?>
          </tbody>
        </table>
      </div>
        <?php if ($pageOrPlugin != 'Plugin'){ ?><div class="row">
        <form method="post" enctype="multipart/form-data">
          <input type="submit" class="btn btn-default col-xs-6" name="AddSubmit" id="AddSubmit" value="Add Media" />
          <input type="file" name="UploadFile" class="col-xs-6" id="UploadFile">
        </form>
      </div>  <?php } ?>
    </div>
    <?php if ($pageOrPlugin != 'Plugin'){ ?></div>  <?php } ?>
  <?php if ($pageOrPlugin != 'Plugin'){ ?></div><?php }
  }
  
  function sub_UI_media_page_FindAndPrintFileData($pageOrPlugin) //This handles the file data for the UI for the Media page.
  {
    foreach(array_filter(glob('../Uploads'.'/*'),'is_file') as $file)
    {
      if (strcasecmp(substr($file,-20),'apple-touch-icon.png') == 0 || strcasecmp(substr($file,-11),'favicon.ico') == 0)
      {
        if ($pageOrPlugin != 'Plugin')
          echo '<tr><td></td><td></td><td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td></tr>';
        //don't do anything to show in the plugin.  It'll just confuse people.
      }
      else if (strcasecmp(substr($file,-4),'.png') == 0 || strcasecmp(substr($file,-4),'.jpg') == 0 || strcasecmp(substr($file,-5),'.jpeg') == 0 || strcasecmp(substr($file,-4),'.bmp') == 0 || strcasecmp(substr($file,-4),'.gif') == 0 || strcasecmp(substr($file,-5),'.tiff') == 0)
      {
        echo '<tr><td><img src="' . PROTOCOL . URL . substr($file,2) . '" alt="' . substr($file,11) . '" style="height:8vh;width:auto;" /></td><td>&ltimg src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; alt=&quot;' . substr($file,11) . '&quot; /&gt;</td>' . '<td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
        if ($pageOrPlugin != 'Plugin')
        {
          echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $returnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
        }
        echo '</tr>';
      }
      else if (strcasecmp(substr($file,-4),'.mp4') == 0 || strcasecmp(substr($file,-5),'.webm') == 0 || strcasecmp(substr($file,-4),'.ogv') == 0)
      {
        echo '<tr><td>No Image Available.</td><td>&lt;video controls&gt;&ltsource src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; /&gt;Your Web Browser Doesn&#39;t Support Videos!&lt;/video&gt;</td>' . '<td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
        if ($pageOrPlugin != 'Plugin')
        {
          echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $returnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
        }
        echo '</tr>';
      }
      else if (strcasecmp(substr($file,-4),'.mp3') == 0 || strcasecmp(substr($file,-4),'.ogg') == 0)
      {
        echo '<tr><td>No Image Available.</td><td>&lt;audio controls&gt;&ltsource src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; /&gt;Your Web Browser Doesn&#39;t Support Audio!&lt;/audio&gt;</td>' . '<td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
        if ($pageOrPlugin != 'Plugin')
        {
          echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $returnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
        }
        echo '</tr>';
      }
      else
      {
        echo '<tr><td>No Image Available.</td>' . '<td>' . substr($file,2) . '</td><td>&lta href=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; title=&quot;' . substr($file,11) . '&quot; &gt;' . PROTOCOL . URL . substr($file,2) . '&lt;/a&gt;</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
        if ($pageOrPlugin != 'Plugin')
        {
          echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $returnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
        }
        echo '</tr>';
      }
    }
  }
  
  function engine_media_plugin() //This handles the Media page plugin.
  {
    UI_media_page('Plugin');
  }

  function engine_register_page() //This handles the data for the Register page.
  {
    if (isset($_POST['RegisterSubmit']))
    {
      $dBConnection = connect();
      if(isset($_POST['Username']) && isset($_POST['Email']) && !empty($_POST['Username']) && !empty($_POST['Email']) && isset($_POST['Password1']) && !empty($_POST['Password1']) && isset($_POST['Password2']) && !empty($_POST['Password2']))
      {
        $safeUsername = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Username']),"UTF-8"));
        $safeEmail = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Email']),"UTF-8"));
        $safePassword1 = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Password1']),"UTF-8"));
        $safePassword2 = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Password2']),"UTF-8"));
        if (isset($_POST['Company']) && !empty($_POST['Company']))
          $safeCompany = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Company']),"UTF-8"));
        if (isset($_POST['Website']) && !empty($_POST['Website']))
          $safeWebsite = mysqli_real_escape_string($dBConnection,mb_convert_encoding(htmlspecialchars($_POST['Website']),"UTF-8"));
        if (isset($_POST['EmailIsPublic']) && !empty($_POST['EmailIsPublic']))
          $safeEmailIsPublic = 1;
        else
          $safeEmailIsPublic = 0;
        if ($safePassword1 == $safePassword2)
        {
          $safePassword1 = password_hash($safePassword1, PASSWORD_DEFAULT);
          if ($safeEmailIsPublic == 1)
          {
            $dBQuery = "INSERT INTO `" . DBPREFIX . "_LoginTable` (Username,Password,Email,Company,URL,EmailIsPublic) VALUES ('" . $safeUsername . "','" . $safePassword1 . "','" . $safeEmail . "','" . $safeCompany . "','" . $safeWebsite . "',1);";
            mysqli_query($dBConnection,$dBQuery);
            echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Account Added!</strong></p></div>';
          }
          else
          {
            $dBQuery = "INSERT INTO `" . DBPREFIX . "_LoginTable` (Username,Password,Email,Company,URL,EmailIsPublic) VALUES ('" . $safeUsername . "','" . $safePassword1 . "','" . $safeEmail . "','" . $safeCompany . "','" . $safeWebsite . "',0);";
            mysqli_query($dBConnection,$dBQuery);
            echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Account Added!</strong></p></div>';
          }
        }
        else
          echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Passwords Do Not Match.</strong></p></div>';
      }
      else
        echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>You need at least username, password, and email address for an account.</strong></p></div>';
      disconnect($dBConnection);
    }
    //Call in the UI, and pass variables to autofill the form
    UI_register_page();
  }

  function UI_register_page() //This handles the UI for the Register page.
  {
  ?><div class="container-fluid">
  <div class="row">
    <form method="post" id="RegisterForm" class="form-horizontal col-xs-10 col-xs-push-1">
      <fieldset class="form-group">
        <legend>New Account:</legend>
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Username">Username*:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Username" id="Username" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Password1">Password*:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="password" class="form-control" name="Password1" id="Password1" />
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-9 col-sm-push-3">
            <input type="password" class="form-control" name="Password2" id="Password2" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Company">Company:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Company" id="Company" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="UserURL">Website:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="UserURL" id="UserURL" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="Email">Email*:</label> 
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" name="Email" id="Email" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-xs-12 col-sm-3" for="EmailPublic">Make email address public:</label> 
          <div class="col-xs-12 col-sm-9">
            <div class="checkbox">
              <input type="checkbox" name="EmailPublic" id="EmailPublic" />
            </div>
          </div>
        </div>
        <br />
        <div class="row">
          <input type="submit" class="btn btn-default col-xs-3" name="RegisterSubmit" id="RegisterSubmit" value="Add New Account" />
        </div>
      </fieldset>
    </form>
  </div>
</div><?php
  }
  
  function engine_logout_page($safeCookie) //This handles the data for the Logout page.  It needs no UI.
  {
    $dBConnection = connect();
    $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Cookie = 'XXX' WHERE Cookie = '" . $safeCookie . "';";
    mysqli_query($dBConnection,$dBQuery);
    disconnect($dBConnection);
    echo 'Logging You Out Now...  <script>window.location.href = "' . PROTOCOL . URL . '/Back/";</script>';
  }
?>
