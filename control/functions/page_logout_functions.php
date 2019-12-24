<?php
/**
 * This handles the data processing for the Logout page.  It needs no UI.
 * @param safeCookie - The authentication cookie in use.
 **/
function engine_logout_page($safeCookie)
{
  $dBConnection = connect();
  $dBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Cookie = 'XXX' WHERE Cookie = '" . $safeCookie . "';";
  mysqli_query($dBConnection,$dBQuery);
  disconnect($dBConnection);
  echo 'Logging You Out Now...  <script>window.location.href = "' . PROTOCOL . URL . '/control/";</script>';
}
?>