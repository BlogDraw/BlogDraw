<?php
/**
 * index.php - this is the default page called by users accessing the site.  It passes all of the work on to the relevant functions in the functions.php file, and initialises the chosen template.
**/
	require_once ('./functions.php');
	require_once ('./template/' . TEMPLATE . '/index.php');
?>
