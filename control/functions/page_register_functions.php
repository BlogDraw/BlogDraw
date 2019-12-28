<?php
/**
 * This handles the data processing for the Register page.
 **/
function engine_register_page() //This handles the data for the Register page.
{
  if (isset($_POST['RegisterSubmit']))
  {
    $dBConnection = connect();
    if(isset($_POST['Username']) && isset($_POST['DisplayName']) && isset($_POST['Email']) && !empty($_POST['Username']) && !empty($_POST['Email']) && isset($_POST['Password1']) && !empty($_POST['Password1']) && isset($_POST['Password2']) && !empty($_POST['Password2']))
    {
      $safeUsername = cleanString($dBConnection, $_POST['Username']);
      $safeUsername = cleanString($dBConnection, $_POST['DisplayName']);
      $safeEmail = cleanString($dBConnection, $_POST['Email']);
      $safePassword1 = cleanString($dBConnection, $_POST['Password1']);
      $safePassword2 = cleanString($dBConnection, $_POST['Password2']);
      if (isset($_POST['Company']) && !empty($_POST['Company']))
        $safeCompany = cleanString($dBConnection, $_POST['Company']);
      if (isset($_POST['Website']) && !empty($_POST['Website']))
        $safeWebsite = cleanString($dBConnection, $_POST['Website']);
      if (isset($_POST['EmailIsPublic']) && !empty($_POST['EmailIsPublic']))
        $safeEmailIsPublic = 1;
      else
        $safeEmailIsPublic = 0;
      if ($safePassword1 == $safePassword2)
      {
        $safePassword1 = password_hash($safePassword1, PASSWORD_DEFAULT);
        if ($safeEmailIsPublic == 1)
        {
          $dBQuery = "INSERT INTO `" . DBPREFIX . "_LoginTable` (Username,DisplayName,Password,Email,Company,URL,EmailIsPublic) VALUES ('" . $safeUsername . "','" . $safeDisplayName . "','" . $safePassword1 . "','" . $safeEmail . "','" . $safeCompany . "','" . $safeWebsite . "',1);";
          mysqli_query($dBConnection,$dBQuery);
          echo '<div class="row"><p class="col-10 offset-1"><strong>Account Added!</strong></p></div>';
        }
        else
        {
          $dBQuery = "INSERT INTO `" . DBPREFIX . "_LoginTable` (Username,DisplayName,Password,Email,Company,URL,EmailIsPublic) VALUES ('" . $safeUsername . "','" . $safeDisplayName . "','" . $safePassword1 . "','" . $safeEmail . "','" . $safeCompany . "','" . $safeWebsite . "',0);";
          mysqli_query($dBConnection,$dBQuery);
          echo '<div class="row"><p class="col-10 offset-1"><strong>Account Added!</strong></p></div>';
        }
      }
      else
        echo '<div class="row"><p class="col-10 offset-1"><strong>Passwords Do Not Match.</strong></p></div>';
    }
    else
      echo '<div class="row"><p class="col-10 offset-1"><strong>You need at least username, display name, password, and email address for an account.</strong></p></div>';
    disconnect($dBConnection);
  }
  //Call in the UI.
  UI_register_page();
}

/**
 * This handles the UI for the Register page.
 **/
function UI_register_page()
{
?><div class="container-fluid">
  <div class="row">
    <form method="post" id="RegisterForm" class="col-10 offset-1">
      <fieldset class="form-group">
        <legend>New Account:</legend>
        <div class="row">
          <label class="control-label col-12 col-sm-3" for="Username">Username*:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="Username" id="Username" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-12 col-sm-3" for="DisplayName">Display Name*:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="DisplayName" id="DisplayName" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-12 col-sm-3" for="Password1">Password*:</label>
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
          <label class="control-label col-12 col-sm-3" for="Company">Company:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="Company" id="Company" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-12 col-sm-3" for="UserURL">Website:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="UserURL" id="UserURL" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-12 col-sm-3" for="Email">Email*:</label>
          <div class="col-12 col-sm-9">
            <input type="text" class="form-control" name="Email" id="Email" />
          </div>
        </div>
        <br />
        <div class="row">
          <label class="control-label col-12 col-sm-3" for="EmailPublic">Make email address public:</label>
          <div class="col-12 col-sm-9">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="EmailPublic" id="EmailPublic" />
            </div>
          </div>
        </div>
        <br />
        <div class="row">
          <input type="submit" class="btn btn-light col-3" name="RegisterSubmit" id="RegisterSubmit" value="Add New Account" />
        </div>
      </fieldset>
    </form>
  </div>
</div><?php
}
?>