<?php
/**
 * This handles the data processing for the Account page.
 * @param safeCookie - The authentication cookie in use.
 **/
function engine_account_page($safeCookie)
{
  $dBConnection = connect();
  $dBQuery = "SELECT Username,DisplayName,Email,UserImage,UserBlurb,Company,URL,EmailIsPublic FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
  {
    $returnedUsername = cleanHtmlString($dBConnection, $row['Username']);
    $returnedDisplayName = cleanHtmlString($dBConnection, $row['DisplayName']);
    $returnedEmail = cleanHtmlString($dBConnection, $row['Email']);
    $returnedUserImage = cleanHtmlString($dBConnection, $row['UserImage']);
    $returnedUserBlurb = str_replace("\\\"", "&quot;", $returnedUserBlurb); // User Blurb may contain speech marks.  This is intended behaviour.
    $returnedCompany = cleanHtmlString($dBConnection, $row['Company']);
    $returnedURL = cleanHtmlString($dBConnection, $row['URL']);
    $returnedEmailIsPublic = cleanHtmlString($dBConnection, $row['EmailIsPublic']);
  }
  disconnect($dBConnection);

  if (isset($_POST['AccountSubmit']))
  {
    $dBConnection = connect();
    if (isset($_POST['Username']) && isset($_POST['DisplayName']) && isset($_POST['Email']) && !empty($_POST['Username']) && !empty($_POST['Email']))
    {
      $safeUsername = cleanString($dBConnection, $_POST['Username']);
      $safeDisplayName = cleanString($dBConnection, $_POST['DisplayName']);
      $safeEmail = cleanString($dBConnection, $_POST['Email']);
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
      echo '<div class="row"><p class="col-10 offset-1"><strong>You need at least username and email address for an account.</strong></p></div>';

    if (isset($_POST['Password1']) && isset($_POST['Password2']) && !empty($_POST['Password1']))
    {
      if ($_POST['Password1'] == $_POST['Password2'])
      {
        $newPassword = password_hash($_POST['Password1'], PASSWORD_DEFAULT);
        $safePassword = cleanHtmlString($dBConnection,$newPassword);
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Password = '" . $safePassword . "' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        echo '<div class="row"><p class="col-10 offset-1"><strong>Your account password has been reset, please look after it.</strong></p></div>';
      }
      else
        echo '<div class="row"><p class="col-10 offset-1"><strong>Passords don&#39;t match!</strong></p></div>';
    }
    if (isset($_POST['Company']))
    {
      $safeCompany = cleanString($dBConnection, $_POST['Company']);
      if ($safeCompany != $returnedCompany)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Company = '" . $safeCompany . "' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        $returnedCompany = $safeCompany;
      }
    }
    if (isset($_POST['DisplayName']))
    {
      $safeDisplayName = cleanString($dBConnection, $_POST['DisplayName']);
      if ($safeDisplayName != $returnedDisplayName)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET DisplayName = '" . $safeDisplayName . "' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        $returnedDisplayName = $safeDisplayName;
      }
    }
    if (isset($_POST['UserURL']))
    {
      $safeURL = cleanHtmlString($dBConnection, $_POST['UserURL']);
      if ($safeURL != $returnedURL)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET URL = '" . $safeURL . "' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        $returnedURL = $safeURL;
      }
    }
    if (isset($_POST['UserImage']))
    {
      $safeUserImage = cleanHtmlString($dBConnection, $_POST['UserImage']);
      if ($safeUserImage != $returnedUserImage)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET UserImage = '" . $safeUserImage . "' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        $returnedUserImage = $safeUserImage;
      }
    }
    if (isset($_POST['UserBlurb']))
    {
      $safeUserBlurb = cleanHtmlString($dBConnection, $_POST['UserBlurb']);
      if ($safeUserBlurb != $returnedUserBlurb)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET UserBlurb = '" . $safeUserBlurb . "' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        $returnedUserBlurb = str_replace("\\'", "'", $safeUserBlurb);
      }
    }
    if (isset($_POST['EmailPublic']))
    {
      if ($returnedEmailIsPublic == 0)
      {
        $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET EmailIsPublic = '1' WHERE Cookie = '" . $safeCookie . "';";
        mysqli_query($dBConnection,$dBQuery);
        $returnedEmailIsPublic = 1;
      }
    }
    else if (!isset($_POST['EmailPublic']))
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
  UI_account_page($returnedUsername,$returnedDisplayName,$returnedCompany,$returnedURL,$returnedEmail,$returnedUserBlurb,$returnedUserImage,$returnedEmailIsPublic);
}

/**
 * This handles the UI for the Account page.
 * @param returnedUsername - The username returned from the database.
 * @param returnedDisplayName - The display name returned from the database.
 * @param returnedCompany - The company returned from the database.
 * @param returnedURL - The URL returned from the database.
 * @param returnedEmail - The email address returned from the database.
 * @param returnedUserBlurb - The user's blurb returned from the database.
 * @param returnedUserImage - The user's image returned from the database.
 * @param returnedEmailIsPublic - The boolean value representing whether the user's email is public returned from the database.
 **/
function UI_account_page($returnedUsername,$returnedDisplayName,$returnedCompany,$returnedURL,$returnedEmail,$returnedUserBlurb,$returnedUserImage,$returnedEmailIsPublic)
{
?><div class="container-fluid">
  <div class="row">
    <form method="post" id="AccountChangeForm" class="col-10 offset-1">
      <fieldset class="form-group">
        <legend>My Account:</legend>
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="Username">Username:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="Username" id="Username" value="<?php echo $returnedUsername; ?>" />
          </div>
        </div>
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="DisplayName">Display Name:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="DisplayName" id="DisplayName" value="<?php echo $returnedDisplayName; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="Password1">Change Password:</label>
          <div class="col-12 col-sm-9">
            <input type="password" class="form-control" name="Password1" id="Password1" />
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-sm-9 offset-sm-3">
            <input type="password" class="form-control" name="Password2" id="Password2" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="Company">Company:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="Company" id="Company" value="<?php echo $returnedCompany; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="UserURL">Website:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="UserURL" id="UserURL" value="<?php echo $returnedURL; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="Email">Email:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="Email" id="Email" value="<?php echo $returnedEmail; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="UserImage">User Photo URL:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="UserImage" id="UserImage" value="<?php echo $returnedUserImage; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="UserBlurb">Your User Blurb (accepts HTML):</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="UserBlurb" id="UserBlurb" value="<?php echo $returnedUserBlurb; ?>" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="col-form-label col-12 col-sm-3" for="EmailPublic">Make email address public:</label>
          <div class="col-12 col-sm-9">
            <div class="form-check">
                <?php if ($returnedEmailIsPublic == 1){ ?>
              <input class="form-check-input" type="checkbox" name="EmailPublic" id="EmailPublic" checked />
                <?php }else{ ?>
              <input class="form-check-input" type="checkbox" name="EmailPublic" id="EmailPublic" />
                <?php } ?>
            </div>
          </div>
        </div>
        <br />
        <div class="row">
          <input type="submit" class="btn btn-light col-3" name="AccountSubmit" value="Change" />
        </div>
      </fieldset>
    </form>
  </div>
</div><?php
}
?>
