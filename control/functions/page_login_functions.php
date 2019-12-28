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
    $safeUsername = cleanString($dBConnection, $_POST['Username']);
    $safePassword = cleanString($dBConnection, $_POST['Password']);
    while($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    {
      $returnedUsername = cleanHtmlString($dBConnection, $row['Username']);
      $returnedPassword = cleanHtmlString($dBConnection, $row['Password']);
      $returnedID = cleanHtmlString($dBConnection, $row['ID']);
      if ($returnedUsername == $safeUsername)
      {
        if (password_verify($safePassword,$returnedPassword))
        {
          $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Cookie = '" . $safeCookie . "' WHERE ID = '" . $returnedID . "';";
          mysqli_query($dBConnection,$dBQuery);
          echo '<script>window.location.href = "' . PROTOCOL . URL . '/control/";</script>';
          return;
        }
      }
    }
    echo '<div class="row"><p class="col-10 offset-1"><strong>Username and/or Password is Invalid.</strong></p></div>';
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
  <form method="post" id="LoginForm" class="col-10 offset-1">
    <fieldset class="form-group">
      <legend>Login:</legend>
      <div class="row">
        <label class="control-label col-12 col-sm-3" for="Username">Username:</label>
        <div class="col-12 col-sm-9">
          <input type="text" class="form-control" name="Username" id="Username" />
        </div>
      </div>
      <br />
      <div class="row">
        <label class="control-label col-12 col-sm-3" for="Password">Password:</label>
        <div class="col-12 col-sm-9">
          <input type="password" class="form-control" name="Password" id="Password" />
        </div>
      </div>
      <br />
      <div class="row">
        <input type="submit" class="btn btn-light col-3" name="LoginSubmit" value="Login" />
      </div>
    </fieldset>
  </form>
</div>
</div>
<?php
}
?>