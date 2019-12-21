<?php
require_once ('db_connection_handler.php');
/**
 * functions_back.php - this combines the core PHP functions that operate the back end of BlogDraw (Known as "The Back").
 * They are split up as follows:
 * - Functions named engine_... - these contain the code that runs each page or aspect of a page - the complex algorithms.
 * - Functions named UI_... - these contain the code for the User Interfaces (UIs) of each page.  We need to keep these in PHP instead of HTML as many of them need dynamically generated content.
 * - Functions named sub_... - these contain extra logic needed for the function they're relevant to, but need their own function for readability, portability, memory management, etc...
**/
require_once ('page_account_functions.php');
require_once ('page_login_functions.php');
require_once ('page_add_posts_functions.php');
require_once ('page_edit_posts_functions.php');
require_once ('add_edit_posts_functions.php');
require_once ('page_media_functions.php');
require_once ('page_register_functions.php');
require_once ('page_logout_functions.php');

?>
