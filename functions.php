<?php
// DO NOT EDIT BELOW THIS LINE!-----------------------------------------
	global $NotLoggedIn;	
					
	if (!isset($_POST['LoginSubmit']))
	{
		$CookieKey = mb_convert_encoding(htmlspecialchars(bin2hex(random_bytes(256))),"UTF-8");
	}
	if (isset($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']))
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		$SafeCookie = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']), "UTF-8"));
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT Cookie FROM `" . DBPREFIX . "_LoginTable` WHERE CHAR_LENGTH(Cookie) > 1;";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedCookie = mb_convert_encoding($Row['Cookie'], "UTF-8");
			if ($ReturnedCookie == $SafeCookie)
			{
				$NotLoggedIn = false;
			}
		}
		mysqli_close($DBConnection);
	}
	if (!isset($_POST['LoginSubmit']) && (!isset($_COOKIE[preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin']) || $NotLoggedIn == true))
	{
		setcookie(preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', TITLE))) . 'BlogDrawLogin',$CookieKey,0,'/',URL,FALSE,TRUE);	
	}
	
	function engine_analytics_collector()
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$IP = mysqli_real_escape_string($DBConnection,mb_convert_encoding($_SERVER["REMOTE_ADDR"], "UTF-8"));
		$Page = mysqli_real_escape_string($DBConnection,mb_convert_encoding(PROTOCOL . URL . $_SERVER['REQUEST_URI'], "UTF-8"));
		$Month = mysqli_real_escape_string($DBConnection,mb_convert_encoding(date("n"), "UTF-8"));
		$Year = mysqli_real_escape_string($DBConnection,mb_convert_encoding(date("Y"), "UTF-8"));
		$DBQuery = "INSERT INTO `" . DBPREFIX . "_AnalyticsTable` (`IP`,`Page`,`Month`,`Year`) VALUES ('$IP', '$Page', '$Month','$Year');";
		mysqli_query($DBConnection,$DBQuery);
		mysqli_close($DBConnection);
	}
	
// HEAD FUNCTIONS	
	function output_head_title()
	{
		$URI = ltrim($_SERVER['REQUEST_URI'], '/');
		if ($URI == NULL)
		{
			echo TITLE;
		}
		else if (substr($URI,0,4) == "tag-")
		{
			echo TITLE . ' | Tags | ' . urldecode(substr($URI,4));
		}
		else switch ($URI)
		{
			case 'archive':
				echo TITLE . ' | Archive';
				break;
			case 'contact':
				echo TITLE . ' | Contact';
				break;
			default:
				$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
				if (!$DBConnection)
				{
					die('Could not connect to database.  Please try again later.');
				}
				$IsPost = false;
				$DBQuery = "SELECT Title FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $URI . "' ORDER BY ID DESC LIMIT 1;";
				$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
				while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
				{
					$ReturnedTitle = mb_convert_encoding(htmlspecialchars($Row['Title']), "UTF-8");
					echo TITLE . ' | ' . $ReturnedTitle;
					$IsPost = true;
				}
				mysqli_close($DBConnection);
				if($IsPost == false)
				{
					echo TITLE . ' | ' . $URI;
				}
		}
	}
	
	function output_head_description()
	{
		echo DESCRIPTION;
	}
	
	function output_head_template_location()
	{
		echo PROTOCOL . URL . '/template/' . TEMPLATE;
	}
	
//BODY FUNCTIONS
	function output_site_title($HasDescription)
	{
		if ($HasDescription)
		{
			echo '<h1>' . TITLE . ' <small>' . DESCRIPTION . '</small></h1>';
		}
		else
		{
			echo '<h1>' . TITLE . '</h1>';
		}
	}
	
	function output_contact_details($HasEmail,$HasPhone)
	{
		if ($HasEmail && $HasPhone)
		{
			echo '<p>You can contact ' . TITLE . ' by email at: <a href="mailto:' . CONTACTEMAIL . '" title="Email ' . TITLE . '">' . CONTACTEMAIL . '</a> or by phone at: <a href="tel:' . CONTACTPHONE . '" title="Phone ' . TITLE . '">' . CONTACTPHONE . '</a>.</p>';
		}
		else if ($HasEmail && !$HasPhone)
		{
			echo '<p>You can contact ' . TITLE . ' by email at: <a href="mailto:' . CONTACTEMAIL . '" title="Email ' . TITLE . '">' . CONTACTEMAIL . '</a>.</p>';
		}
		else if (!$HasEmail && $HasPhone)
		{
			echo '<p>You can contact ' . TITLE . ' by phone at: <a href="tel:' . CONTACTPHONE . '" title="Phone ' . TITLE . '">' . CONTACTPHONE . '</a>.</p>';
		}
		else
		{
			echo '<p>This Website has not set their contact details.</p>';
		}
	}
	
	function output_archive_link()
	{
		echo PROTOCOL . URL . '/archive';
	}
	
	function output_contact_link()
	{
		echo PROTOCOL . URL . '/contact';
	}
	
	function output_home_link()
	{
		echo PROTOCOL . URL;
	}

	function output_template_location()
	{
		output_head_template_location();
	}

	function output_latest_blog_post()
	{
		$PostID = engine_find_latest_public_post_id();
		engine_collate_post_details($PostID);
	}
	
	function output_blog_archive($NumberToLoad,$LazyLoadIsOK)
	{
		$LastPostLoaded = engine_load_blog_archive($NumberToLoad);
		if($LazyLoadIsOK)
		{
			if (!($LastPostLoaded <= $NumberToLoad))
			{
				engine_load_blog_archive_button($LastPostLoaded);
			}
		}
		else
		{
			engine_load_blog_archive_alt($LastPostLoaded);
		}
	}
	
	function output_canonical_page()
	{
		$URLPath = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
		$Elements = explode('/', $URLPath);                // Split path on slashes
		if(empty($Elements[0]))
		{                       // No path elements means home
			engine_call_canonical_page('home');
		}
		else if(substr($Elements[0],0,4) == "tag-")
		{
			engine_call_canonical_page('tag');
		}
		else switch(array_shift($Elements))             // Pop off first item and switch
		{
			case 'archive':
				engine_call_canonical_page('archive');
				break;
			case 'contact':
				engine_call_canonical_page('contact');
				break;
			default:
				engine_call_canonical_page('post');
		}
	}
	
	function output_author_profile($Option)
	{
		list($AuthorID,$AuthorBlurb,$AuthorImage,$Preamble) = engine_author_profile();
		if ($Option =="Preamble")
		{
			echo $Preamble;
		}
		else if ($Option =="Caption")
		{
			echo engine_call_author_details($AuthorID);
		}
		else if ($Option =="Image")
		{
			if ($AuthorImage !== 'X')
			{
				echo'<img src="' . $AuthorImage . '" style="display:block;margin:0 auto;border-radius:50%;max-width:100%;" />' ;
			}
		}
		else if ($Option =="Blurb")
		{
			if ($AuthorBlurb !== 'X')
			{
				echo  $AuthorBlurb;
			}
		}
		else //if ($Option =="All")
		{
			echo '<aside><header>' . engine_call_author_details($AuthorID) . '</header>';
			if ($AuthorImage !== 'X')
			{
				echo'<img src="' . $AuthorImage . '" style="display:block;margin:0 auto;border-radius:50%;max-width:100%;" />' ;
			}
			if ($AuthorBlurb !== 'X')
			{
				echo  $AuthorBlurb;
			}
			echo '</aside>';
		}
	}
	
//BACKEND FUNCTIONS
	function engine_author_profile()
	{
		$PostID = 1;
		//Check if front page
		$RequestedURI= mb_convert_encoding(htmlspecialchars(substr($_SERVER['REQUEST_URI'],1)), "UTF-8");
		if (PROTOCOL . URL . $RequestedURI == PROTOCOL.URL || PROTOCOL . URL . "/" . $RequestedURI == PROTOCOL.URL."/archive"  || PROTOCOL . URL . "/" . $RequestedURI == PROTOCOL.URL."/contact" || substr(PROTOCOL . URL . "/" . $RequestedURI,0,(LENGTH+5)) == PROTOCOL.URL."/tag-")
		{//If front page, get author id from latest blog article where not draft
			$PostID = engine_find_latest_public_post_id();
			$Preamble = "The latest blog author on this site:";
		}
		else
		{//else find canonical page post link get author id from post where that = nice-title
			$Preamble = "Author profile:";
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$RequestedURI = mysqli_real_escape_string($DBConnection,$RequestedURI);
			$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $RequestedURI . "' ORDER BY ID DESC LIMIT 1;";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedID = mb_convert_encoding(htmlspecialchars($Row['ID']), "UTF-8");
			}
			$PostID = $ReturnedID;
			mysqli_close($DBConnection);
		}
		$AuthorID = engine_call_post_field($PostID,"AuthorID");
		//find author profile from id
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT UserImage,UserBlurb FROM `" . DBPREFIX . "_LoginTable` WHERE ID='" . $AuthorID . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedAuthorImage = mb_convert_encoding(htmlspecialchars($Row['UserImage']), "UTF-8");
			$ReturnedAuthorBlurb = mb_convert_encoding($Row['UserBlurb'], "UTF-8");
		}
		if (!empty($ReturnedAuthorImage)){$AuthorImage = $ReturnedAuthorImage;} else {$AuthorImage = "X";}
		if (!empty($ReturnedAuthorBlurb)){$AuthorBlurb = $ReturnedAuthorBlurb;} else {$AuthorBlurb = "X";}
		mysqli_close($DBConnection);
		return array ($AuthorID,$AuthorBlurb,$AuthorImage,$Preamble);
	}

	function engine_call_canonical_page($Page)
	{
		switch($Page)
		{
			case 'home':
				require_once ('./template/' . TEMPLATE . '/home.php');
				break;
			case 'archive':
				require_once ('./template/' . TEMPLATE . '/archive.php');
				break;
			case 'contact':
				require_once ('./template/' . TEMPLATE . '/contact.php');
				break;
			case 'tag':
				engine_find_called_tag();
				break;
			case 'post':
				engine_find_called_post();
				break;
			case '404':
				echo 'Error 404: Page not found.  Better luck next time';
				break;
			default:
				echo 'Page content error.';
		}
	}

	function engine_find_called_post()
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$PostCount = 1;
		$RequestedURI= mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars(substr($_SERVER['REQUEST_URI'],1)), "UTF-8"));
		$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND NiceTitle='" . $RequestedURI . "' ORDER BY ID DESC LIMIT 1;";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedID = mb_convert_encoding(htmlspecialchars($Row['ID']), "UTF-8");
			engine_collate_post_details($ReturnedID);
			$PostCount = 0;
		}
		mysqli_close($DBConnection);
		if ($PostCount == 1)
		{
			engine_call_canonical_page('404');
		}
	}
	
	function engine_find_called_tag()
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$PostCount = 1;
		$RequestedTag= mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars(urldecode(substr($_SERVER['REQUEST_URI'],5))), "UTF-8"));
		$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND (TagOne='" . $RequestedTag . "' OR TagTwo='" . $RequestedTag . "' OR TagThree='" . $RequestedTag . "') ORDER BY ID DESC;";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedID = mb_convert_encoding(htmlspecialchars($Row['ID']), "UTF-8");
			engine_collate_post_details($ReturnedID);
			$PostCount = 0;
		}
		mysqli_close($DBConnection);
		if ($PostCount == 1)
		{
			engine_call_canonical_page('404');
		}
	}

	function engine_load_blog_archive_button($LastPostLoaded)
	{
		?>
		<form method="post">
			<input id="LastPostLoaded" name="LastPostLoaded" type="hidden" value="<?php echo $LastPostLoaded; ?>" />
			<input type="submit" class="btn btn-default" name="LoadMore" value="Load More..." />
		</form>
		<?php
	}

	function engine_load_blog_archive($NumberToLoad)
	{
		if(isset($_POST['LoadMore']) && isset($_POST['LastPostLoaded']))
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$LastOneLoaded = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['LastPostLoaded']), "UTF-8"));
			if (!is_numeric($LastOneLoaded))
			{
				$LastOneLoaded = $NumberToLoad + 1;
			}
			$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND ID<" . $LastOneLoaded . " ORDER BY ID DESC LIMIT " . $NumberToLoad . ";";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedID = mb_convert_encoding(htmlspecialchars($Row['ID']), "UTF-8");
				engine_collate_post_details($ReturnedID);
			}
			mysqli_close($DBConnection);
		}
		else
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 ORDER BY ID DESC LIMIT " . $NumberToLoad . ";";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedID = mb_convert_encoding(htmlspecialchars($Row['ID']), "UTF-8");
				engine_collate_post_details($ReturnedID);
			}
			mysqli_close($DBConnection);
		}
		return $ReturnedID;
	}
	function engine_load_blog_archive_alt($NumberLeft)
	{
		if($NumberLeft > 0)
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$DBQuery = "SELECT ID,Title,NiceTitle FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 AND ID<" . $NumberLeft . " ORDER BY ID DESC;";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				echo '<a href="' . URL . htmlspecialchars($Row['NiceTitle']) . '" title="' . htmlspecialchars($Row['Title']) . '">' . htmlspecialchars($Row['Title']) . '</a><br />';
			}
			mysqli_close($DBConnection);
		}
	}

	function engine_find_latest_public_post_id()
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_PostsTable` WHERE PostIsDraft=0 ORDER BY ID DESC LIMIT 1;";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedID = mb_convert_encoding(htmlspecialchars($Row['ID']), "UTF-8");
		}
		mysqli_close($DBConnection);
		return $ReturnedID;
	}

	function engine_collate_post_details($PostID)
	{
		echo '<article>';
		echo '<header><h2>' . engine_call_post_field($PostID,"Title") . '</h2></header>';
		$PostAuthor = engine_call_post_field($PostID,"AuthorID");
		$PostAuthor = engine_call_author_details($PostAuthor);
		$PostTagOne = engine_call_post_field($PostID,"TagOne");
		$PostTagTwo = engine_call_post_field($PostID,"TagTwo");
		$PostTagThree = engine_call_post_field($PostID,"TagThree");
		echo '<p><small>Written by: ' . $PostAuthor . ' on: ' . engine_call_post_field($PostID,"TimeStamp") . '.  
			Tags: <a href="' . PROTOCOL . URL . '/tag-' . urlencode($PostTagOne) . '" title="Visit Tag Archive for: ' . $PostTagOne . '">' . $PostTagOne . '</a>, 
			<a href="' . PROTOCOL . URL . '/tag-' . urlencode($PostTagTwo) . '" title="Visit Tag Archive for: ' . $PostTagTwo . '">' . $PostTagTwo . '</a>, 
			<a href="' . PROTOCOL . URL . '/tag-' . urlencode($PostTagThree) . '" title="Visit Tag Archive for: ' . $PostTagThree . '">' . $PostTagThree . '</a>.</small></p>';
		echo engine_call_post_field($PostID,"Post");
		echo '</article>';	
	}

	function engine_call_author_details($PostAuthor)
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT Username,Email,EmailIsPublic,URL FROM `" . DBPREFIX . "_LoginTable` WHERE ID='" . $PostAuthor . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedUsername = mb_convert_encoding(htmlspecialchars($Row['Username']), "UTF-8");
			$ReturnedEmail = mb_convert_encoding(htmlspecialchars($Row['Email']), "UTF-8");
			$ReturnedEmailIsPublic = mb_convert_encoding(htmlspecialchars($Row['EmailIsPublic']), "UTF-8");
			$ReturnedURL = mb_convert_encoding(htmlspecialchars($Row['URL']), "UTF-8");
		}
		mysqli_close($DBConnection);
		if($ReturnedEmailIsPublic == 1 && !empty($ReturnedURL))
		{
			$AuthorCaption = '<a href="' . $ReturnedURL . '" title="Go To ' . $ReturnedURL . '">' . $ReturnedUsername . '</a>(<a href="mailto:' . $ReturnedEmail . '" title="Email ' . $ReturnedEmail . '">Email The Author</a>)';
		}
		else if($ReturnedEmailIsPublic == 1 && empty($ReturnedURL))
		{
			$AuthorCaption = '<a href="mailto:' . $ReturnedEmail . '" title="Email ' . $ReturnedEmail . '">' . $ReturnedUsername . '</a>';
		}
		else if($ReturnedEmailIsPublic == 0 && !empty($ReturnedURL))
		{
			$AuthorCaption = '<a href="' . $ReturnedURL . '" title="Go To ' . $ReturnedURL . '">' . $ReturnedUsername . '</a>';
		}
		else //if($ReturnedEmailIsPublic == 0 && empty($ReturnedURL))
		{
			$AuthorCaption = $ReturnedUsername;
		}
		return $AuthorCaption;
	}

	function engine_call_post_field($PostToCallID,$Field)
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT " . $Field . ",PostIsDraft FROM `" . DBPREFIX . "_PostsTable` WHERE ID='" . $PostToCallID . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedPostIsDraft = mb_convert_encoding(htmlspecialchars($Row['PostIsDraft']), "UTF-8");
			if ($ReturnedPostIsDraft == 0)
			{
				if ($Field != 'Post')
				{
					$ReturnedField = mb_convert_encoding(htmlspecialchars($Row[$Field]), "UTF-8");
				}
				else
				{
					$ReturnedField = mb_convert_encoding($Row[$Field], "UTF-8");
				}
			}
		}
		mysqli_close($DBConnection);
		return $ReturnedField;
	}
	
?>
