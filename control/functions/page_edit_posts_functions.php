<?php
/**
 * This handles the data processing for the Edit Posts page.
 * @param safeCookie - The authentication cookie in use.
 **/
function engine_edit_posts_page($safeCookie)
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

/**
 * This handles the Submit or Draft buttons present on the Edit Posts page.
 * @param submitOrDraft - Holds either submit or draft depending on which button is in use.
 * @param safeCookie - The authentication cookie in use.
 **/
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
      echo '<div class="row"><div class="col-xs-10 col-xs-push-1"><p class="alert alert-success" role="alert">Posted!</p></div></div>
      <script>$(document).ready(function(){window.open(' . PROTOCOL . URL . '/' . $safeNiceTitle . ', "_blank");});</script>';
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
      echo '<div class="row"><div class="col-xs-10 col-xs-push-1"><p class="alert alert-success" role="alert">Saved!</p></div></div>';
    }
  }
  disconnect($dBConnection);
}

/**
 * This handles the UI for the Edit Posts page.
 * @param safeCookie - The authentication cookie in use.
 **/
function UI_edit_posts_page($safeCookie)
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

/**
 * This handles filling in the table on the Edit Posts page.
 * @param safeCookie - The authentication cookie in use.
 **/
function sub_UI_edit_posts_TableContent($safeCookie)
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
?>