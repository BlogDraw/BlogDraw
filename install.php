<?php
	function engine_page()
	{
		if (isset($_POST['Submit']))
		{
			if (!(isset($_POST['DBUsername']) && !empty($_POST['DBUsername']) && isset($_POST['DBPassword']) && !empty($_POST['DBPassword']) && isset($_POST['DBServer']) && !empty($_POST['DBServer']) && isset($_POST['DBName']) && !empty($_POST['DBName']) && isset($_POST['DBPrefix']) && !empty($_POST['DBPrefix']) && isset($_POST['WSTitle']) && !empty($_POST['WSTitle']) && isset($_POST['WSDescription']) && !empty($_POST['WSDescription']) && isset($_POST['WSURL']) && !empty($_POST['WSURL']) && ((isset($_POST['WSSSL']) && !empty($_POST['WSSSL'])) || (isset($_POST['WSnoSSL']) && !empty($_POST['WSnoSSL'])) )))
			{
				echo '<p>Please fill in all fields marked with a &#39;*&#39;.</p>';
			}
			else
			{
				$DBUsername = mb_convert_encoding($_POST['DBUsername'], "UTF-8");
				$DBPassword = mb_convert_encoding($_POST['DBPassword'], "UTF-8");
				$DBServer = mb_convert_encoding($_POST['DBServer'], "UTF-8");
				$DBName = mb_convert_encoding($_POST['DBName'], "UTF-8");
				$DBPrefix = mb_convert_encoding($_POST['DBPrefix'], "UTF-8");
				$WSTitle = mb_convert_encoding($_POST['WSTitle'], "UTF-8");
				$WSDescription = mb_convert_encoding($_POST['WSDescription'], "UTF-8");
				$WSURL = mb_convert_encoding($_POST['WSURL'], "UTF-8");
				if (isset($_POST['WSSSL']) && $_POST['WSSSL'] == "WSnoSSL")
				{
					$WSSSL = 'http://';
					$Length = strlen($WSURL) + 7;
					$ProtoLength = 7;
				}
				else if (isset($_POST['WSSSL']) && $_POST['WSSSL'] == "WSSSL")
				{
					$WSSSL = 'https://';
					$Length = strlen($WSURL) + 8;
					$ProtoLength = 8;
				}
				else
				{
					echo '<p>Error: did you choose HTTP, or HTTPS?</p>';
					return;
				}
				if (isset($_POST['WSContactEmail']) && !empty($_POST['WSContactEmail']))
				{
					$WSContactEmail = mb_convert_encoding($_POST['WSContactEmail'], "UTF-8");
				}
				if (isset($_POST['WSContactPhone']) && !empty($_POST['WSContactPhone']))
				{
					$WSContactPhone = mb_convert_encoding($_POST['WSContactPhone'], "UTF-8");
				}
				//DoTheInstall
				//1. Load the data into the functions file
				$File = "functions.php";
				$FileContent = '<?php' . "\n";
				$FileContent .= '	if (substr($_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"], 0, ' . ($Length + 1 - $ProtoLength) . ') != "' . $WSURL . '/" || substr($_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"], 0, ' . ($Length + 1 - $ProtoLength) . ') != "' . $WSURL . '/")' . "\n";
				$FileContent .= '	{' . "\n" . '		die();' . "\n" . '	}' . "\n";
				$FileContent .= '	define(\'URL\',\'' . $WSURL . '\');				//Website Location.' . "\n";
				$FileContent .= '	define(\'PROTOCOL\',\'' . $WSSSL . '\');			//http:// or https://.' . "\n";
				$FileContent .= '	define(\'LENGTH\',' . $Length . ');					//Length of URL plus PROTOCOL.' . "\n";
				$FileContent .= '	define(\'DBUSER\', \'' . $DBUsername . '\');		//MySQL User.' . "\n";
				$FileContent .= '	define(\'DBPASS\', \'' . $DBPassword . '\');			//MySQL Password.' . "\n";
				$FileContent .= '	define(\'DBSERVER\', \'' . $DBServer . '\');		//Database Server.' . "\n";
				$FileContent .= '	define(\'DBNAME\', \'' . $DBName . '\');		//Database Name.' . "\n";
				$FileContent .= '	define(\'DBPREFIX\', \'' . $DBPrefix . '\');				//Database Table Prefix.' . "\n" . "\n";
				$FileContent .= '	define(\'TITLE\', \'' . $WSTitle . '\');						//Your Blog Title.' . "\n";
				$FileContent .= '	define(\'DESCRIPTION\', \'' . $WSDescription . '\');//A short description of your Blog.'. "\n";
				$FileContent .= '	define(\'CONTACTEMAIL\', \'' . $WSContactEmail . '\');		//Your Contact Email.' . "\n";
				$FileContent .= '	define(\'CONTACTPHONE\', \'' . $WSContactPhone . '\');			//Your Contact Phone.' . "\n";
				$FileContent .= '	define(\'TEMPLATE\', \'BlogDraw2018\');					//Your Template Name.' . "\n";
				$FileContent .= '	define(\'TEMPLATEBY\', \'TuxSoft Limited\');			//Template Manufacturer.' . "\n";
				$FileContent .= '	define(\'COOKIENOTICE\', \'By using this site, you agree to our use of cookies on your computer, which enable some features of the site.\');	//Your Cookie Notice.' . "\n";
				$FileContent .= '?>';
				$FileContent .= file_get_contents($File);
				file_put_contents($File, $FileContent);
				if ($WSSSL == "https://")
				{
					$HtaccessFile = ".htaccess";
					$HtaccessContent = file($HtaccessFile);
					foreach($HtaccessContent as $LineNumber => &$LineContent)
					{
						if($LineNumber == 12)
						{
							$LineContent .= '# Force SSL/TLS';
						}
						if($LineNumber == 13)
						{
							$LineContent .= 'RewriteCond %{HTTPS} off';
						}
						if($LineNumber == 14)
						{
							$LineContent .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]';
						}
					}
					$AllContent = implode("", $HtaccessContent);
					file_put_contents($HtaccessFile, $AllContent);
				}
				else //$WSSSL == "http://"
				{
					$HtaccessFile = ".htaccess";
					$HtaccessContent = file($HtaccessFile);
					foreach($HtaccessContent as $LineNumber => &$LineContent)
					{
						if($LineNumber == 12)
						{
							$LineContent .= '# Force no SSL/TLS';
						}
						if($LineNumber == 13)
						{
							$LineContent .= 'RewriteCond %{HTTPS} on';
						}
						if($LineNumber == 14)
						{
							$LineContent .= 'RewriteRule ^(.*)$ http://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]';
						}
					}
					$AllContent = implode("", $HtaccessContent);
					file_put_contents($HtaccessFile, $AllContent);
				}
				//2. Build the DB
				$RandPass = mt_rand(1000,9999);
				$DBConnection = mysqli_connect($DBServer,$DBUsername,$DBPassword,$DBName);
				if (!$DBConnection)
				{
					die('Could not connect to database.  Please try again later.');
				}
				$DBQuery = "CREATE TABLE " . $DBPrefix . "_LoginTable(ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,Username VARCHAR(25) NOT NULL,Password VARCHAR(256) NOT NULL,Email VARCHAR(255) NOT NULL,Company VARCHAR(50),URL VARCHAR(255),UserImage VARCHAR(255),UserBlurb LONGTEXT,EmailIsPublic BOOLEAN,Cookie VARCHAR(512));";
				mysqli_query($DBConnection,$DBQuery);
				$DBQuery = "INSERT INTO " . $DBPrefix . "_LoginTable (Username,Password,Email,EmailIsPublic,Cookie) VALUES ('Admin','" . password_hash($RandPass . $DBPrefix, PASSWORD_DEFAULT) . "','" . $WSContactEmail . "',0,'XXXX');";
				mysqli_query($DBConnection,$DBQuery);
				$DBQuery = "CREATE TABLE " . $DBPrefix . "_PostsTable(ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,AuthorID BIGINT NOT NULL,Title VARCHAR(128) NOT NULL,NiceTitle VARCHAR(128) NOT NULL,TagOne VARCHAR(512) NOT NULL,TagTwo VARCHAR(512) NOT NULL,TagThree VARCHAR(512),Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,Post LONGTEXT NOT NULL,PostIsDraft BOOLEAN);";
				mysqli_query($DBConnection,$DBQuery);
				$DBQuery = "CREATE TABLE " . $DBPrefix . "_AnalyticsTable(ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,Page VARCHAR(256) NOT NULL,IP VARCHAR(32) NOT NULL,DateTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,Month INT(11) NOT NULL,Year BIGINT NOT NULL);";
				mysqli_query($DBConnection,$DBQuery);
				mysqli_close($DBConnection);
				//OUTPUT
				echo '<p>Username: Admin - Password: ' . $RandPass . $DBPrefix . '.</p><p>Please Log in to ' . $WSURL . '/Back/ and change these details now.</p>';
				
				//3. Delete self
				unlink(__FILE__);
			}
		}
		else
		{
			UI_page();
		}
	}
	function UI_page()
	{
	?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Install BlogDraw</title>
		<!-- Bootstrap -->
		<link rel="stylesheet" href="./Back/bootstrap-3.3.7-dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="./Back/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" />
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<form method="post" id="InstallForm" class="form-horizontal col-xs-10 col-xs-push-1">
					<fieldset class="form-group">
						<legend>Database Details:</legend>
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="DBUsername">Username*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="DBUsername" id="DBUsername" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="DBPassword">Password*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="password" class="form-control" name="DBPassword" id="DBPassword" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="DBServer">Server IP*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="DBServer" id="DBServer" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="DBName">Database Name*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="DBName" id="DBName" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="DBPrefix">Table Prefix (Two random letters, IE: "BD")*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="DBPrefix" id="DBPrefix" />
							</div>
						</div>
						<br />
					</fieldset>
					<fieldset class="form-group">
						<legend>Website Details:</legend>
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="WSURL">Website URL*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="WSURL" id="WSURL" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="WSSSL">HTTP, or HTTPS?*:</label> 
							<div class="col-xs-12 col-sm-9">
								<div>
									<input type="radio" name="WSSSL" value="WSSSL" checked />HTTPS
									<input type="radio" name="WSSSL" value="WSnoSSL" />HTTP
								</div>
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="WSTitle">Website Title*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="WSTitle" id="WSTitle" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="WSDescription">Website Description*:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="WSDescription" id="WSDescription" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="WSContactEmail">Website Contact Email:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="WSContactEmail" id="WSContactEmail" />
							</div>
						</div>
						<br />
						<div class="row">
							<label class="control-label col-xs-12 col-sm-3" for="WSContactPhone">Website Contact Phone:</label> 
							<div class="col-xs-12 col-sm-9">
								<input type="text" class="form-control" name="WSContactPhone" id="WSContactPhone" />
							</div>
						</div>
						<br />
					</fieldset>
					<div class="row">
						<input type="submit" class="btn btn-default col-xs-3" name="Submit" id="Submit" value="Submit" />
					</div>
				</form>
			</div>
		</div>
		<br />
	</body>
	<!-- jQuery and Bootstrap -->
	<script src="./Back/bootstrap-3.3.7-dist/js/jquery-3.2.1.min.js"></script>
	<script src="./Back/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
</html><?php
	}
	engine_page();
?>
