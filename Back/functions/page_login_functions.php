<?php
/**
 * This handles the data processing for the Login page.
 * @param safeCookie - The authentication cookie in use.
 **/
function engine_login_page($safeCookie)
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

/**
 * This handles the UI for the Login page.
 **/
function UI_login_page()
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
?>