<?php
/**
 * Connects to a database. 
 * @return Database connection object.
 **/
function connect()
{
  $dBConnection = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
  if (!$dBConnection)
    die("<p>BlogDraw couldn't connect to the database.</p>");
  return $dBConnection;
}

/**
 * Closes an open database connection.
 * @param $dBConnection - The Database connection object.
 **/
function disconnect($dBConnection)
{
  if (!empty($dBConnection))
    mysqli_close($dBConnection);
}

/**
 * Prevents xss/SQL injection 
 * @param $dBConnection - The database connection object currently in use.
 * @param $string - The String to clean.
 * @return the string. 
 **/
function cleanString($dBConnection, $string)
{
  $str = mysqli_real_escape_string($dBConnection, mb_convert_encoding(htmlspecialchars($string), "UTF-8"));
  return $str;// Return the string.
}
/**
 * Prevents xss/SQL injection - doesn't strip HTML tags.
 * @param $dBConnection - The database connection object currently in use.
 * @param $string - The String to clean.
 * @return the string. 
 **/
function cleanHtmlString($dBConnection, $string)
{
  $str = mysqli_real_escape_string($dBConnection, mb_convert_encoding($string, "UTF-8"));
  return $str;// Return the string.
}
?>