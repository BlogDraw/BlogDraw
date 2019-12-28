<?php
/**
 * install.php - this installs and initialises BlogDraw for the first time.
 **/
  function engine_page() //This handles all of the data for the install process.
  {
    if (isset($_POST['Submit']))
    {
      if (!(isset($_POST['DBUsername']) && !empty($_POST['DBUsername']) && isset($_POST['DBPassword']) && !empty($_POST['DBPassword']) && isset($_POST['DBServer']) && !empty($_POST['DBServer']) && isset($_POST['DBName']) && !empty($_POST['DBName']) && isset($_POST['DBPrefix']) && !empty($_POST['DBPrefix']) && isset($_POST['WSTitle']) && !empty($_POST['WSTitle']) && isset($_POST['WSDescription']) && !empty($_POST['WSDescription']) && isset($_POST['WSURL']) && !empty($_POST['WSURL']) && ((isset($_POST['WSSSL']) && !empty($_POST['WSSSL'])) || (isset($_POST['WSnoSSL']) && !empty($_POST['WSnoSSL'])) )))
        echo '<p>Please fill in all fields marked with a &#39;*&#39;.</p>';
      else
      {
        $dBUsername = mb_convert_encoding($_POST['DBUsername'], "UTF-8");
        $dBPassword = mb_convert_encoding($_POST['DBPassword'], "UTF-8");
        $dBServer = mb_convert_encoding($_POST['DBServer'], "UTF-8");
        $dBName = mb_convert_encoding($_POST['DBName'], "UTF-8");
        $dBPrefix = mb_convert_encoding($_POST['DBPrefix'], "UTF-8");
        $wSTitle = mb_convert_encoding($_POST['WSTitle'], "UTF-8");
        $wSDescription = mb_convert_encoding($_POST['WSDescription'], "UTF-8");
        $wSURL = mb_convert_encoding($_POST['WSURL'], "UTF-8");
        if (isset($_POST['WSSSL']) && $_POST['WSSSL'] == "WSnoSSL")
        {
          $wSSSL = 'http://';
          $length = strlen($wSURL) + 7;
          $protoLength = 7;
        }
        else if (isset($_POST['WSSSL']) && $_POST['WSSSL'] == "WSSSL")
        {
          $wSSSL = 'https://';
          $length = strlen($wSURL) + 8;
          $protoLength = 8;
        }
        else
        {
          echo '<p>Error: did you choose HTTP, or HTTPS?</p>';
          return;
        }
        if (isset($_POST['WSContactEmail']) && !empty($_POST['WSContactEmail']))
          $wSContactEmail = mb_convert_encoding($_POST['WSContactEmail'], "UTF-8");
        if (isset($_POST['WSContactPhone']) && !empty($_POST['WSContactPhone']))
          $wSContactPhone = mb_convert_encoding($_POST['WSContactPhone'], "UTF-8");
        //The data has now been sanitised, Start the installation process.
        //1. Load the Website data into the functions file
        $file = "functions.php";
        $fileContent = '<?php' . "\n";
        $fileContent .= 'if (substr($_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"], 0, ' . ($length + 1 - $protoLength) . ') != "' . $wSURL . '/" || substr($_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"], 0, ' . ($length + 1 - $protoLength) . ') != "' . $wSURL . '/")' . "\n";
        $fileContent .= '  die();' . "\n";
        $fileContent .= 'define(\'URL\', \'' . $wSURL . '\');        //Website Location.' . "\n";
        $fileContent .= 'define(\'PROTOCOL\', \'' . $wSSSL . '\');      //http:// or https://.' . "\n";
        $fileContent .= 'define(\'LENGTH\', ' . $length . ');          //Length of URL plus PROTOCOL.' . "\n";
        $fileContent .= 'define(\'DBUSER\', \'' . $dBUsername . '\');    //MySQL User.' . "\n";
        $fileContent .= 'define(\'DBPASS\', \'' . $dBPassword . '\');      //MySQL Password.' . "\n";
        $fileContent .= 'define(\'DBSERVER\', \'' . $dBServer . '\');    //Database Server.' . "\n";
        $fileContent .= 'define(\'DBNAME\', \'' . $dBName . '\');    //Database Name.' . "\n";
        $fileContent .= 'define(\'DBPREFIX\', \'' . $dBPrefix . '\');        //Database Table Prefix.' . "\n" . "\n";
        $fileContent .= 'define(\'TITLE\', \'' . $wSTitle . '\');            //Your Blog Title.' . "\n";
        $fileContent .= 'define(\'DESCRIPTION\', \'' . $wSDescription . '\');//A short description of your Blog.'. "\n";
        $fileContent .= 'define(\'CONTACTEMAIL\', \'' . $wSContactEmail . '\');    //Your Contact Email.' . "\n";
        $fileContent .= 'define(\'CONTACTPHONE\', \'' . $wSContactPhone . '\');      //Your Contact Phone.' . "\n";
        $fileContent .= 'define(\'TEMPLATE\', \'BlogDraw2020\');          //Your Template Name.' . "\n";
        $fileContent .= 'define(\'TEMPLATEBY\', \'TuxSoft Limited\');      //Template Manufacturer.' . "\n";
        $fileContent .= 'define(\'COOKIENOTICE\', \'By using this site, you agree to our use of cookies on your computer, which enable some features of the site.\');  //Your Cookie Notice.' . "\n";
        $fileContent .= '?>';
        $fileContent .= file_get_contents($file);
        file_put_contents($file, $fileContent);
        if ($wSSSL == "https://")
        {
          $htaccessFile = ".htaccess";
          $htaccessContent = file($htaccessFile);
          foreach($htaccessContent as $lineNumber => &$lineContent)
          {
            if($lineNumber == 12)
              $lineContent .= '# Force SSL/TLS';
            if($lineNumber == 13)
              $lineContent .= 'RewriteCond %{HTTPS} off';
            if($lineNumber == 14)
              $lineContent .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]';
          }
          $allContent = implode("", $htaccessContent);
          file_put_contents($htaccessFile, $allContent);
        }
        else //$wSSSL == "http://"
        {
          $htaccessFile = ".htaccess";
          $htaccessContent = file($htaccessFile);
          foreach($htaccessContent as $lineNumber => &$lineContent)
          {
            if($lineNumber == 12)
              $lineContent .= '# Force no SSL/TLS';
            if($lineNumber == 13)
              $lineContent .= 'RewriteCond %{HTTPS} on';
            if($lineNumber == 14)
              $lineContent .= 'RewriteRule ^(.*)$ http://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]';
          }
          $allContent = implode("", $htaccessContent);
          file_put_contents($htaccessFile, $allContent);
        }
        //2. Build the database structure, and populate the login table.
        $randPass = mt_rand(1000, 9999);
        $dBConnection = mysqli_connect($dBServer, $dBUsername, $dBPassword, $dBName);
        if (!$dBConnection)
          die('Could not connect to database.  Please try again later.');
        $dBQuery = "CREATE TABLE " . $dBPrefix . "_LoginTable(ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, Username VARCHAR(25) NOT NULL, DisplayName VARCHAR(25) NOT NULL, Password VARCHAR(256) NOT NULL, Email VARCHAR(255) NOT NULL, Company VARCHAR(50), URL VARCHAR(255), EmailIsPublic BOOLEAN, Cookie VARCHAR(512), UserImage VARCHAR(255), UserBlurb LONGTEXT);";
        mysqli_query($dBConnection, $dBQuery);
        $dBQuery = "INSERT INTO " . $dBPrefix . "_LoginTable (Username, DisplayName, Password, Email, EmailIsPublic, Cookie) VALUES ('Admin', 'Administrator', '" . password_hash($randPass . $dBPrefix, PASSWORD_DEFAULT) . "', '" . $wSContactEmail . "', 0, 'XXXX');";
        mysqli_query($dBConnection, $dBQuery);
        $dBQuery = "CREATE TABLE " . $dBPrefix . "_PostsTable(ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, AuthorID BIGINT NOT NULL, Title VARCHAR(128) NOT NULL, NiceTitle VARCHAR(128) NOT NULL, TagOne VARCHAR(512) NOT NULL, TagTwo VARCHAR(512) NOT NULL, TagThree VARCHAR(512), Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, Post LONGTEXT NOT NULL, PostIsDraft BOOLEAN);";
        mysqli_query($dBConnection, $dBQuery);
        $dBQuery = "INSERT INTO " . $dBPrefix . "_PostsTable (AuthorID, Title, NiceTitle, TagOne, TagTwo, TagThree, Post, PostIsDraft) VALUES (1, 'First Post', 'first-post', 'BlogDraw', 'First', 'Post', '<div>Welcome to my new blog!</div>', false);";
        mysqli_query($dBConnection, $dBQuery);
        mysqli_close($dBConnection);
        //OUTPUT
        echo '<p>Username: Admin - Password: ' . $randPass . $dBPrefix . '.</p><p>Please Log in to ' . $wSURL . '/control/ and change these details now.</p>';
       
        //3. Delete this script, to stop other people using this script to destroy the site.
        unlink(__FILE__);
      }
    }
    else
      UI_page();
  }
  
  function UI_page() //This handles the UI for the installer.
  {
  ?><!DOCTYPE html>
<html lang="en">
  <head>
    <title>Install BlogDraw</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./control/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./control/bootstrap/css/bootstrap-theme.min.css" />
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <form method="post" id="InstallForm" class="col-10 offset-1">
          <fieldset class="form-group">
            <legend>Database Details:</legend>
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="DBUsername">Username*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="DBUsername" id="DBUsername" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="DBPassword">Password*:</label>
              <div class="col-12 col-sm-9">
                <input type="password" class="form-control" name="DBPassword" id="DBPassword" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="DBServer">Server IP*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="DBServer" id="DBServer" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="DBName">Database Name*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="DBName" id="DBName" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="DBPrefix">Table Prefix (Two random letters, IE: "BD")*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="DBPrefix" id="DBPrefix" />
              </div>
            </div>
            <br />
          </fieldset>
          <fieldset class="form-group">
            <legend>Website Details:</legend>
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="WSURL">Website URL*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="WSURL" id="WSURL" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="WSSSL">HTTP, or HTTPS?*:</label>
              <div class="form-check col-12 col-sm-9">
                <div>
                  <input class="form-check-input" type="radio" name="WSSSL" value="WSSSL" checked />HTTPS&nbsp;
                  <input class="form-check-input" type="radio" name="WSSSL" value="WSnoSSL" />HTTP
                </div>
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="WSTitle">Website Title*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="WSTitle" id="WSTitle" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="WSDescription">Website Description*:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="WSDescription" id="WSDescription" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="WSContactEmail">Website Contact Email:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="WSContactEmail" id="WSContactEmail" />
              </div>
            </div>
            <br />
            <div class="row">
              <label class="control-label col-12 col-sm-3" for="WSContactPhone">Website Contact Phone:</label>
              <div class="col-12 col-sm-9">
                <input type="text" class="form-control" name="WSContactPhone" id="WSContactPhone" />
              </div>
            </div>
            <br />
          </fieldset>
          <div class="row">
            <input type="submit" class="btn btn-primary col-3" name="Submit" id="Submit" value="Submit" />
          </div>
        </form>
      </div>
    </div>
    <br />
  </body>
  <!-- jQuery and Bootstrap -->
  <script src="./control/bootstrap/js/jquery-3.4.1.min.js"></script>
  <script src="./control/bootstrap/js/bootstrap.min.js"></script>
</html><?php
  }
  engine_page();
?>
