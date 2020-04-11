<?php
/**
 * Connects to a database.
 * @return dBConnection - Database connection object.
 **/
function connect()
{
  $dBConnection = mysqli_connect(DBSERVER, DBUSER, DBPASS, DBNAME);
  if (!$dBConnection)
    die("<p>BlogDraw couldn't connect to the database.</p>");
  return $dBConnection;
}

/**
 * Closes an open database connection.
 * @param dBConnection - The Database connection object.
 **/
function disconnect($dBConnection)
{
  if (!empty($dBConnection))
    mysqli_close($dBConnection);
}

/**
 * Prevents xss/SQL injection
 * @param dBConnection - The database connection object currently in use.
 * @param string - The String to clean.
 * @return string - The string.
 **/
function cleanString($dBConnection, $string)
{
  $string = mysqli_real_escape_string($dBConnection, mb_convert_encoding(htmlspecialchars($string), "UTF-8"));
  return $string;// Return the string.
}

/**
 * Prevents xss/SQL injection - doesn't strip HTML tags.
 * @param dBConnection - The database connection object currently in use.
 * @param string - The String to clean.
 * @return string - The string.
 **/
function cleanHtmlString($dBConnection, $string)
{
  $string = mysqli_real_escape_string($dBConnection, mb_convert_encoding($string, "UTF-8"));
  return $string;// Return the string.
}
?>