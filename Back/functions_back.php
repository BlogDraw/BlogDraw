<?php
/**
 * functions_back.php - this contains most of the core PHP functions that operate the back end of BlogDraw (Known as "The Back").
 * They are split up as follows:
 * - Functions named engine_... - these contain the code that runs each page or aspect of a page - the complex algorithms.
 * - Functions named UI_... - these contain the code for the User Interfaces (UIs) of each page.  We need to keep these in PHP instead of HTML as many of them need dynamically generated content.
 * - Functions named sub_... - these contain extra logic needed for the function they're relevant to, but need their own function for readability, portability, memory management, etc...
**/
	function engine_account_page($SafeCookie) //This handles the data for the Account page.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT Username,Email,UserImage,UserBlurb,Company,URL,EmailIsPublic FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedUsername = mb_convert_encoding($Row['Username'], "UTF-8");
			$ReturnedEmail = mb_convert_encoding($Row['Email'], "UTF-8");
			$ReturnedUserImage = mb_convert_encoding($Row['UserImage'], "UTF-8");
			$ReturnedUserBlurb = mb_convert_encoding($Row['UserBlurb'], "UTF-8");
			$ReturnedCompany = mb_convert_encoding($Row['Company'], "UTF-8");
			$ReturnedURL = mb_convert_encoding($Row['URL'], "UTF-8");
			$ReturnedEmailIsPublic = mb_convert_encoding($Row['EmailIsPublic'], "UTF-8");
		}
		mysqli_close($DBConnection);

		if (isset($_POST['AccountSubmit']))
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			if(isset($_POST['Username']) && isset($_POST['Email']) && !empty($_POST['Username']) && !empty($_POST['Email']))
			{
				$SafeUsername = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Username']),"UTF-8"));
				$SafeEmail = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Email']),"UTF-8"));
				if ($SafeUsername != $ReturnedUsername)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Username = '" . $SafeUsername . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedUsername = $SafeUsername;
				}
				if ($SafeEmail != $ReturnedEmail)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Email = '" . $SafeEmail . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedEmail = $SafeEmail;
				}
			}
			else
			{
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>You need at least username and email address for an account.</strong></p></div>';
			}
		
			if(isset($_POST['Password1']) && isset($_POST['Password2']) && !empty($_POST['Password1']))
			{
				if ($_POST['Password1'] == $_POST['Password2'])
				{
					$NewPassword = password_hash($_POST['Password1'], PASSWORD_DEFAULT);
					$SafePassword = mysqli_real_escape_string($DBConnection,$NewPassword);
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Password = '" . $SafePassword . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Your account password has been reset, please look after it.</strong></p></div>';
				}
				else
				{
					echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Passords don&#39;t match!</strong></p></div>';
				}
			}
			if(isset($_POST['Company']))
			{
				$SafeCompany = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Company']),"UTF-8"));
				if ($SafeCompany != $ReturnedCompany)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Company = '" . $SafeCompany . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedCompany = $SafeCompany;
				}
			}
			if(isset($_POST['UserURL']))
			{
				$SafeURL = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['UserURL']),"UTF-8"));
				if ($SafeURL != $ReturnedURL)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET URL = '" . $SafeURL . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedURL = $SafeURL;
				}
			}
			if(isset($_POST['UserImage']))
			{
				$SafeUserImage = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['UserImage']),"UTF-8"));
				if ($SafeUserImage != $ReturnedUserImage)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET UserImage = '" . $SafeUserImage . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedUserImage = $SafeUserImage;
				}
			}
			if(isset($_POST['UserBlurb']))
			{
				$SafeUserBlurb = mysqli_real_escape_string($DBConnection,mb_convert_encoding($_POST['UserBlurb'],"UTF-8"));
				if ($SafeUserBlurb != $ReturnedUserBlurb)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET UserBlurb = '" . $SafeUserBlurb . "' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedUserBlurb = $SafeUserBlurb;
				}
			}
			if(isset($_POST['EmailPublic']))
			{
				if ($ReturnedEmailIsPublic == 0)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET EmailIsPublic = '1' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedEmailIsPublic = 1;
				}
			}
			else if(!isset($_POST['EmailPublic']))
			{
				if ($ReturnedEmailIsPublic == 1)
				{
					$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET EmailIsPublic = '0' WHERE Cookie = '" . $SafeCookie . "';";
					mysqli_query($DBConnection,$DBQuery);
					$ReturnedEmailIsPublic = 0;
				}
			}
			mysqli_close($DBConnection);
		}
		//Call in the UI, and pass variables to autofill the form
		UI_account_page($ReturnedUsername,$ReturnedCompany,$ReturnedURL,$ReturnedEmail,$ReturnedUserBlurb,$ReturnedUserImage,$ReturnedEmailIsPublic);
	}

	function UI_account_page($ReturnedUsername,$ReturnedCompany,$ReturnedURL,$ReturnedEmail,$ReturnedUserBlurb,$ReturnedUserImage,$ReturnedEmailIsPublic) //This handles the UI for the Account page.
	{
	?><div class="container-fluid">
	<div class="row">
		<form method="post" id="AccountChangeForm" class="form-horizontal col-xs-10 col-xs-push-1">
			<fieldset class="form-group">
				<legend>My Account:</legend>
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Username">Username:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="Username" id="Username" value="<?php echo $ReturnedUsername; ?>" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Password1">Change Password:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="password" class="form-control" name="Password1" id="Password1" />
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-9 col-sm-push-3">
						<input type="password" class="form-control" name="Password2" id="Password2" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Company">Company:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="Company" id="Company" value="<?php echo $ReturnedCompany; ?>" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="UserURL">Website:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="UserURL" id="UserURL" value="<?php echo $ReturnedURL; ?>" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Email">Email:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="Email" id="Email" value="<?php echo $ReturnedEmail; ?>" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="UserImage">User Photo URL:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="UserImage" id="UserImage" value="<?php echo $ReturnedUserImage; ?>" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="UserBlurb">Your User Blurb (accepts HTML):</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="UserBlurb" id="UserBlurb" value="<?php echo $ReturnedUserBlurb; ?>" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="EmailPublic">Make email address public:</label> 
					<div class="col-xs-12 col-sm-9">
						<div class="checkbox">
							<?php if ($ReturnedEmailIsPublic == 1){ ?>
							<input type="checkbox" name="EmailPublic" id="EmailPublic" checked />
							<?php }else{ ?>
							<input type="checkbox" name="EmailPublic" id="EmailPublic" />
							<?php } ?>
						</div>
					</div>
				</div>
				<br />
				<div class="row">
					<input type="submit" class="btn btn-default col-xs-3" name="AccountSubmit" value="Change" />
				</div>
			</fieldset>
		</form>
	</div>
</div><?php
	}
	
	function engine_login_page($SafeCookie) //This handles the data for the Login page.
	{
		if (isset($_POST['LoginSubmit']))
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$DBQuery = "SELECT ID,Username,Password FROM `" . DBPREFIX . "_LoginTable`;";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			$SafeUsername = mysqli_real_escape_string($DBConnection,mb_convert_encoding($_POST['Username'],"UTF-8"));
			$SafePassword = mysqli_real_escape_string($DBConnection,mb_convert_encoding($_POST['Password'],"UTF-8"));
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedUsername = mb_convert_encoding($Row['Username'], "UTF-8");
				$ReturnedPassword = mb_convert_encoding($Row['Password'], "UTF-8");
				$ReturnedID = mb_convert_encoding($Row['ID'], "UTF-8");
				if ($ReturnedUsername == $SafeUsername)
				{
					if (password_verify($SafePassword,$ReturnedPassword))
					{
						$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Cookie = '" . $SafeCookie . "' WHERE ID = '" . $ReturnedID . "';";
						mysqli_query($DBConnection,$DBQuery);
						echo '<script>window.location.href = "' . PROTOCOL . URL . '/Back/";</script>';
						return;
					}
				}
			}
			echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Username and/or Password is Invalid.</strong></p></div>';
			mysqli_close($DBConnection);
		}
		UI_login_page();
	}
	
	function UI_login_page() //This handles the UI for the Login page.
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
	
	function engine_add_posts_page($SafeCookie) //This handles the data for the "Add Posts" page.
	{
		if (isset($_POST['PostSubmit']))
		{
			if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
			{
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
			}
			else
			{
				sub_engine_add_posts_SubmitOrDraft('Submit',$SafeCookie);
			}
		}
		else if (isset($_POST['PostDraft']))
		{
			if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
			{
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
			}
			else
			{
				sub_engine_add_posts_SubmitOrDraft('Draft',$SafeCookie);
			}
		}
		UI_add_edit_posts_page('Add',$SafeCookie,0);
	}
	
	function sub_engine_add_posts_SubmitOrDraft($SubmitOrDraft,$SafeCookie) //This handles the Submit or Draft buttons present on the "Add Posts" page.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$SafeTitle = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Title']),"UTF-8"));
		$SafeNiceTitle = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $SafeTitle)));
		$SafePost = mysqli_real_escape_string($DBConnection,mb_convert_encoding('<div>' . nl2br($_POST['Content'],true) . '</div>',"UTF-8"));
		$SafeTagList = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Tags']),"UTF-8"));
		$SafeTagArray = explode(',', $SafeTagList);
		$SafeTagOne = $SafeTagArray[0];
		$SafeTagTwo = $SafeTagArray[1];
		$SafeTagThree = $SafeTagArray[2];
	
		$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedAuthorID = mb_convert_encoding($Row['ID'], "UTF-8");
		}
		if ($SubmitOrDraft == 'Submit')
		{
			$DBQuery = "INSERT INTO `" . DBPREFIX . "_PostsTable` (AuthorID,Title,NiceTitle,TagOne,TagTwo,TagThree,Post,PostIsDraft) VALUES ('" . $ReturnedAuthorID . "','" . $SafeTitle . "','" . $SafeNiceTitle . "','" . $SafeTagOne . "','" . $SafeTagTwo . "','" . $SafeTagThree . "','" . $SafePost . "',0);";
			mysqli_query($DBConnection,$DBQuery);
			echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Posted!</strong></p></div>';
		}
		else if ($SubmitOrDraft == 'Draft')
		{
			$DBQuery = "INSERT INTO `" . DBPREFIX . "_PostsTable` (AuthorID,Title,NiceTitle,TagOne,TagTwo,TagThree,Post,PostIsDraft) VALUES ('" . $ReturnedAuthorID . "','" . $SafeTitle . "','" . $SafeNiceTitle . "','" . $SafeTagOne . "','" . $SafeTagTwo . "','" . $SafeTagThree . "','" . $SafePost . "',1);";
			mysqli_query($DBConnection,$DBQuery);
			echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Saved!</strong></p></div>';
		}
		mysqli_close($DBConnection);
	}
	
	function sub_UI_add_edit_posts_FindAuthorDetails ($SafeCookie) //This handles the filling in the author details for the UI for the "Add or Edit Posts" page.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT Username FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedAuthor = mb_convert_encoding($Row['Username'], "UTF-8");
		}
		mysqli_close($DBConnection);
		return '<p>Written by: ' . $ReturnedAuthor . ' on: ' . date("Y-m-d") . '.</p>';
	}
	
	function UI_add_edit_posts_page($AddEdit,$SafeCookie,$EditPostID) //This handles the UI for the "Add or Edit Posts" page.
	{
	?><script>
function controlBoldFunc()
{
	var StartPosition = $('#Content').prop('selectionStart');
	var EndPosition = $('#Content').prop('selectionEnd');
	var ContentValue = $('#Content').val();
	var PreText = ContentValue.substring(0,  StartPosition );
	var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
	$('#Content').val( PreText+ "<strong> BOLD TEXT HERE </strong>" +PostText );
}
			
function controlItalicFunc()
{
	var StartPosition = $('#Content').prop('selectionStart');
	var EndPosition = $('#Content').prop('selectionEnd');
	var ContentValue = $('#Content').val();
	var PreText = ContentValue.substring(0,  StartPosition );
	var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
	$('#Content').val( PreText+ "<em> ITALIC TEXT HERE </em>" +PostText );
}
			
function controlUnderlineFunc()
{
	var StartPosition = $('#Content').prop('selectionStart');
	var EndPosition = $('#Content').prop('selectionEnd');
	var ContentValue = $('#Content').val();
	var PreText = ContentValue.substring(0,  StartPosition );
	var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
	$('#Content').val( PreText+ '<span style="text-decoration:underline;"> UNDERLINED TEXT HERE </span>' +PostText );
}
			
function controlQuoteFunc()
{
	var StartPosition = $('#Content').prop('selectionStart');
	var EndPosition = $('#Content').prop('selectionEnd');
	var ContentValue = $('#Content').val();
	var PreText = ContentValue.substring(0,  StartPosition );
	var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
	$('#Content').val( PreText+ '<blockquote> QUOTE HERE </blockquote>' +PostText );
}
			
function controlCodeFunc()
{
	var StartPosition = $('#Content').prop('selectionStart');
	var EndPosition = $('#Content').prop('selectionEnd');
	var ContentValue = $('#Content').val();
	var PreText = ContentValue.substring(0,  StartPosition );
	var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
	$('#Content').val( PreText+ '<code> CODE HERE </code>' +PostText );
}
</script>
<div class="container-fluid">
	<div class="row">
		<form method="post" id="AccountChangeForm" class="form-horizontal col-xs-10 col-xs-push-1">
			<fieldset class="form-group">
				<?php if ($AddEdit == 'Add'){ ?>
				<legend>Add a Post:</legend>
				<?php } else if ($AddEdit == 'Edit'){ ?>
				<legend>Edit a Post:</legend>
				<?php } ?>
				<div class="row"> 
					<div class="col-xs-12">
						<input type="text" class="form-control" name="Title" id="Title" placeholder="Title" />
					</div>
				</div>
				<div class="row"> 
					<div class="col-xs-12">
						<?php echo sub_UI_add_edit_posts_FindAuthorDetails ($SafeCookie); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-8">
						<fieldset>
							<div>
								<a class="btn btn-default btn-sm" name="controlBold" onclick="controlBoldFunc()">B</a>
								<a class="btn btn-default btn-sm" name="controlItalic" onclick="controlItalicFunc()">I</a>
								<a class="btn btn-default btn-sm" name="controlUnderline" onclick="controlUnderlineFunc()">U</a>
								<a class="btn btn-default btn-sm" name="controlQuote" onclick="controlQuoteFunc()">"</a>
								<a class="btn btn-default btn-sm" name="controlCode" onclick="controlCodeFunc()">&lt;&gt;</a>
							</div>
							<div style="height:0.4rem;"></div>
							<div>
								<textarea class="form-control" rows="12" name="Content" id="Content" ></textarea>
							</div>
							<br />
						</fieldset>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-control" style="height:315px;overflow-y:scroll;"><?php engine_media_plugin(); ?></div>
					</div>
				</div>
				<br />
				<div class="row"> 
					<div class="col-xs-12 col-sm-8">
						<input type="text" class="form-control" name="Tags" id="Tags" placeholder="3 comma separated Tags I.E: blog,post,hello" />
					</div>
				</div>
				<br />
				<?php if ($AddEdit == 'Edit'){ ?><input type="hidden" name="Editor" id="Editor" value="<?php echo $EditPostID; ?>" /> <?php } ?>
				<div class="btn-group col-xs-12">
					<input type="submit" class="btn btn-default col-xs-4" name="PostSubmit" value="Write Post" />
					<input type="submit" class="btn btn-default col-xs-4" name="PostDraft" value="Save Draft Post" />
					<input type="submit" class="btn btn-default col-xs-4" name="PostCancel" value="Cancel Post" />
				</div>
			</fieldset>
		</form>
	</div>
</div><?php
	}
	
	function engine_edit_posts_page($SafeCookie) //This handles the data for the "Edit Posts" page.
	{
		if (isset($_POST['EditSubmit']) && isset($_POST['Edit']) && !empty($_POST['Edit']))
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$SafeEditPostNo = mysqli_real_escape_string($DBConnection,mb_convert_encoding($_POST['Edit'],"UTF-8"));
			$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedID = mb_convert_encoding($Row['ID'], "UTF-8");
			}
			$DBQuery = "SELECT ID,AuthorID,Title,Post,TagOne,TagTwo,TagThree FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $SafeEditPostNo . "';";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedPostID = mb_convert_encoding($Row['ID'], "UTF-8");
				$ReturnedAuthorID = mb_convert_encoding($Row['AuthorID'], "UTF-8");
				$ReturnedTitle = mb_convert_encoding($Row['Title'], "UTF-8");
				$ReturnedPost = mb_convert_encoding($Row['Post'], "UTF-8");
				$ReturnedPost = str_replace("<br />", "", $ReturnedPost); //Writing a post adds in HTML linebreaks.  We want to remove these so we don't add them twice.
				$ReturnedTagOne = mb_convert_encoding($Row['TagOne'], "UTF-8");
				$ReturnedTagTwo = mb_convert_encoding($Row['TagTwo'], "UTF-8");
				$ReturnedTagThree = mb_convert_encoding($Row['TagThree'], "UTF-8");
				$ReturnedTags = $ReturnedTagOne . ',' . $ReturnedTagTwo . ',' . $ReturnedTagThree;
			}
			if ($ReturnedID == $ReturnedAuthorID)
			{
				UI_add_edit_posts_page('Edit',$SafeCookies,$ReturnedPostID);
				sub_UI_add_edit_posts_JSFillForEdit($ReturnedPostID,$ReturnedTitle,$ReturnedPost,$ReturnedTags);
			}
			mysqli_close($DBConnection);
		}
		else if (isset($_POST['DeleteSubmit']) && isset($_POST['Delete']) && !empty($_POST['Delete']))
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			$SafeDeletePostNo = mysqli_real_escape_string($DBConnection,mb_convert_encoding($_POST['Delete'],"UTF-8"));
			$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedID = mb_convert_encoding($Row['ID'], "UTF-8");
			}
			$DBQuery = "SELECT AuthorID FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $SafeDeletePostNo . "';";
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedAuthorID = mb_convert_encoding($Row['AuthorID'], "UTF-8");
			}
			if ($ReturnedID == $ReturnedAuthorID)
			{
				$DBQuery = "DELETE FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $SafeDeletePostNo . "';";
				mysqli_query($DBConnection,$DBQuery);
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Your post has been deleted.</strong></p></div>';
			}
			mysqli_close($DBConnection);
		}
		else
		{
			UI_edit_posts_page($SafeCookie);
		}
		
		//Handle the add or edit UI
		if (isset($_POST['PostSubmit']))
		{
			if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
			{
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
			}
			else
			{
				sub_engine_edit_posts_SubmitOrDraft('Submit',$SafeCookie);
			}
		}
		else if (isset($_POST['PostDraft']))
		{
			if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
			{
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Please fill in all fields.</strong></p></div>';
			}
			else
			{
				sub_engine_edit_posts_SubmitOrDraft('Draft',$SafeCookie);
			}
		}
	}
	
	function sub_engine_edit_posts_SubmitOrDraft($SubmitOrDraft,$SafeCookie) //This handles the Submit or Draft buttons present on the "Edit Posts" page.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$SafeTitle = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Title']),"UTF-8"));
		$SafeNiceTitle = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $SafeTitle)));
		$SafePost = mysqli_real_escape_string($DBConnection,mb_convert_encoding('<div>' . nl2br($_POST['Content'],true) . '</div>',"UTF-8"));
		$SafeTagList = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Tags']),"UTF-8"));
		$SafeEditID = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Editor']),"UTF-8"));
		$SafeTagArray = explode(',', $SafeTagList);
		$SafeTagOne = $SafeTagArray[0];
		$SafeTagTwo = $SafeTagArray[1];
		$SafeTagThree = $SafeTagArray[2];
	
		$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedID = mb_convert_encoding($Row['ID'], "UTF-8");
		}
		if ($SubmitOrDraft == 'Submit')
		{
			$DBQuery = "SELECT AuthorID FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $SafeEditID . "';";
			mysqli_query($DBConnection,$DBQuery);
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedAuthorID = mb_convert_encoding($Row['AuthorID'], "UTF-8");
			}
			if ($ReturnedID == $ReturnedAuthorID)
			{
				$DBQuery = "UPDATE `" . DBPREFIX . "_PostsTable` SET Title = '" . $SafeTitle . "',NiceTitle = '" . $SafeNiceTitle . "',TagOne = '" . $SafeTagOne . "',TagTwo = '" . $SafeTagTwo . "',TagThree = '" . $SafeTagThree . "',Post = '" . $SafePost . "',PostIsDraft = 0 WHERE ID = '" . $SafeEditID . "';";
				mysqli_query($DBConnection,$DBQuery);
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Posted!</strong></p></div>';
			}
		}
		else if ($SubmitOrDraft == 'Draft')
		{
			$DBQuery = "SELECT AuthorID FROM `" . DBPREFIX . "_PostsTable` WHERE ID = '" . $SafeEditID . "';";
			mysqli_query($DBConnection,$DBQuery);
			$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
			while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
			{
				$ReturnedAuthorID = mb_convert_encoding($Row['AuthorID'], "UTF-8");
			}
			if ($ReturnedID == $ReturnedAuthorID)
			{
				$DBQuery = "UPDATE `" . DBPREFIX . "_PostsTable` SET Title = '" . $SafeTitle . "',NiceTitle = '" . $SafeNiceTitle . "',TagOne = '" . $SafeTagOne . "',TagTwo = '" . $SafeTagTwo . "',TagThree = '" . $SafeTagThree . "',Post = '" . $SafePost . "',PostIsDraft = 1 WHERE ID = '" . $SafeEditID . "';";
				mysqli_query($DBConnection,$DBQuery);
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Saved!</strong></p></div>';
			}
		}
		mysqli_close($DBConnection);
	}
	
	function sub_UI_add_edit_posts_JSFillForEdit($ReturnedPostID,$ReturnedTitle,$ReturnedPost,$ReturnedTags) //This fills in the UI form on the "Add or Edit Posts" page if needed.
	{
	?><script>
		document.getElementById("Title").value = "<?php echo $ReturnedTitle; ?>";
		document.getElementById("Content").value = `<?php echo substr($ReturnedPost,5,-6); ?>`;
		document.getElementById("Tags").value = "<?php echo $ReturnedTags; ?>";
	</script><?php
	}
	
	function UI_edit_posts_page($SafeCookie) //This handles the UI for the "Edit Posts" page.
	{?><div class="container-fluid">
	<div class="row">
		<form class="form-horizontal col-xs-10 col-xs-push-1">
			<fieldset class="form-group">
				<legend>View and Edit Posts</legend>
			</fieldset>
		</form>
	</div>
	<div class="row">
		<div class="col-xs-10 col-xs-push-1">
			<div class="table-responsive">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Number:</th>
							<th>Title:</th>
							<th>Content:</th>
							<th>Written On:</th>
							<th>Actions:</th>
						</tr>
					</thead>
					<tbody>
						<?php sub_UI_edit_posts_TableContent($SafeCookie); ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div><?php
	}
	
	function sub_UI_edit_posts_TableContent($SafeCookie) //This fills in the table on the "Edit Posts" page.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $SafeCookie . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedID = mb_convert_encoding($Row['ID'], "UTF-8");
		}
		$DBQuery = "SELECT ID,Title,Post,Timestamp,PostIsDraft FROM `" . DBPREFIX . "_PostsTable` WHERE AuthorID = '" . $ReturnedID . "';";
		$ReturnQuery = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($ReturnQuery, MYSQLI_ASSOC))
		{
			$ReturnedPostID = mb_convert_encoding($Row['ID'], "UTF-8");
			$ReturnedTitle = mb_convert_encoding($Row['Title'], "UTF-8");
			$ReturnedPost = substr(strip_tags(mb_convert_encoding($Row['Post'], "UTF-8")),0,80);
			$ReturnedTimestamp = mb_convert_encoding($Row['Timestamp'], "UTF-8");
			$ReturnedPostIsDraft = mb_convert_encoding($Row['PostIsDraft'], "UTF-8");
			if ($ReturnedPostIsDraft == 1)
			{
				$ReturnedPost = substr("[DRAFT]: " . $ReturnedPost,0,80);
			}
			echo'<tr><td>' . $ReturnedPostID . '</td><td>' . $ReturnedTitle . '</td><td>' . $ReturnedPost . '</td><td>' . $ReturnedTimestamp . '</td><td><form method="post" style="display:inline;"><input id="Edit" name="Edit" type="hidden" value="' . $ReturnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="EditSubmit" value="Edit" /></form>&nbsp;<form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $ReturnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td></tr>';
		}
		mysqli_close($DBConnection);
	}
		
	function engine_media_page() //This handles the data for the Media page.
	{
		if (isset($_POST['DeleteSubmit']) && isset($_POST['Delete']) && !empty($_POST['Delete']))
		{
			$File = htmlspecialchars($_POST['Delete']);
			if (file_exists($File) && substr($File,0,11) == "../Uploads/")
			{				
				unlink($File) or die("Couldn't delete file");
			}
		}
		
		if (isset($_POST['AddSubmit']))
		{
			$TargetDir = "../Uploads/";
			$TargetFile = $TargetDir . basename($_FILES["UploadFile"]["name"]);
			$FineToUpload = 1;
			$FileType = strtolower(pathinfo($TargetFile,PATHINFO_EXTENSION));

			if (file_exists($TargetFile)) //Check if file already exists
			{
				echo "File already exists.";
				$FineToUpload = 0;
			}

			if ($_FILES["UploadFile"]["size"] > 2000000) //Check file size
			{
				echo "Your file is too large.";
				$FineToUpload = 0;
			}

			if(!($FileType == "jpg" || $FileType == "jpeg" || $FileType == "png" || $FileType == "bmp" || $FileType == "gif" || $FileType == "tiff" || $FileType == "ogg" || $FileType == "ogv" || $FileType == "webm" || $FileType == "mp4" || $FileType == "txt" || $FileType == "rtf" || $FileType == "pdf" || $FileType == "docx" || $FileType == "pptx" || $FileType == "xlsx" || $FileType == "csv" || $FileType == "odt" || $FileType == "odp" || $FileType == "ods" || $FileType == "odg" || $FileType == "mp3" || $FileType == "ico")) //Allow certain file formats
			{
				echo "Allowed formats are: jpg, jpeg, png, bmp, gif, tiff, ogg, ogv, webm, mp4, mp3, txt, rtf, pdf, docx, pptx, xlsx, csv, odt, odp, ods, odg, ico.";
				$FineToUpload = 0;
			}

			if ($FineToUpload == 0) //Check if $FineToUpload is set to 0 by an error
			{
				echo "Sorry, your file was not uploaded.";
			}
			else //if everything is ok, try to upload file
			{
				if (move_uploaded_file($_FILES["UploadFile"]["tmp_name"], $TargetFile))
				{
					echo "The file ". basename( $_FILES["UploadFile"]["name"]). " has been uploaded.";
				}
				else
				{
					echo "Sorry, there was an error uploading your file.";
				}
			}
		}
		UI_media_page('Page');
	}
	
	function UI_media_page($PageOrPlugin) //This handles the UI for the Media page, and it's plugin on the "Add or Edit Posts" page.
	{
	?><?php if ($PageOrPlugin != 'Plugin'){ ?><div class="container-fluid">
	<div class="row">
		<form class="form-horizontal col-xs-10 col-xs-push-1">
			<fieldset class="form-group">
				<legend>Your Media:</legend>
			</fieldset>
		</form>
	</div>
	<div class="row"><?php } ?>
		<?php if ($PageOrPlugin != 'Plugin'){ ?><div class="col-xs-10 col-xs-push-1"><?php } else { ?><div class="col-xs-12"><?php } ?>
			<div class="table-responsive">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Image:</th>
							<th>HTML Code (copy into a blog post):</th>
							<th>Location:</th>
							<th>Uploaded on:</th>
							<?php if ($PageOrPlugin != 'Plugin'){ ?><th>Delete:</th><?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php sub_UI_media_page_FindAndPrintFileData($PageOrPlugin); ?>
					</tbody>
				</table>
			</div>
			<?php if ($PageOrPlugin != 'Plugin'){ ?><div class="row">
				<form method="post" enctype="multipart/form-data">
					<input type="submit" class="btn btn-default col-xs-6" name="AddSubmit" id="AddSubmit" value="Add Media" />
					<input type="file" name="UploadFile" class="col-xs-6" id="UploadFile">
				</form>
			</div><?php } ?>
		</div>
	<?php if ($PageOrPlugin != 'Plugin'){ ?></div><?php } ?>
<?php if ($PageOrPlugin != 'Plugin'){ ?></div><?php }
	}
	
	function sub_UI_media_page_FindAndPrintFileData($PageOrPlugin) //This handles the file data for the UI for the Media page.
	{
		foreach(array_filter(glob('../Uploads'.'/*'),'is_file') as $File)
		{
			if (strcasecmp(substr($File,-4),'.png') == 0 || strcasecmp(substr($File,-4),'.jpg') == 0 || strcasecmp(substr($File,-5),'.jpeg') == 0 || strcasecmp(substr($File,-4),'.bmp') == 0 || strcasecmp(substr($File,-4),'.gif') == 0 || strcasecmp(substr($File,-5),'.tiff') == 0)
			{
				echo '<tr><td><img src="' . PROTOCOL . URL . substr($File,2) . '" alt="' . substr($File,11) . '" style="height:8vh;width:auto;" /></td><td>&ltimg src=&quot;' . PROTOCOL . URL . substr($File,2) . '&quot; alt=&quot;' . substr($File,11) . '&quot; /&gt;</td>' . '<td>' . substr($File,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($File)) . '</td>';
				if ($PageOrPlugin != 'Plugin')
				{
					echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $ReturnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
				}
				echo '</tr>';
			}
			else if (strcasecmp(substr($File,-4),'.mp4') == 0 || strcasecmp(substr($File,-5),'.webm') == 0 || strcasecmp(substr($File,-4),'.ogv') == 0)
			{
				echo '<tr><td>No Image Available.</td><td>&lt;video controls&gt;&ltsource src=&quot;' . PROTOCOL . URL . substr($File,2) . '&quot; /&gt;Your Web Browser Doesn&#39;t Support Videos!&lt;/video&gt;</td>' . '<td>' . substr($File,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($File)) . '</td>';
				if ($PageOrPlugin != 'Plugin')
				{
					echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $ReturnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
				}
				echo '</tr>';
			}
			else if (strcasecmp(substr($File,-4),'.mp3') == 0 || strcasecmp(substr($File,-4),'.ogg') == 0)
			{
				echo '<tr><td>No Image Available.</td><td>&lt;audio controls&gt;&ltsource src=&quot;' . PROTOCOL . URL . substr($File,2) . '&quot; /&gt;Your Web Browser Doesn&#39;t Support Audio!&lt;/audio&gt;</td>' . '<td>' . substr($File,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($File)) . '</td>';
				if ($PageOrPlugin != 'Plugin')
				{
					echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $ReturnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
				}
				echo '</tr>';
			}
			else
			{
				echo '<tr><td>No Image Available.</td>' . '<td>' . substr($File,2) . '</td><td>&lta href=&quot;' . PROTOCOL . URL . substr($File,2) . '&quot; title=&quot;' . substr($File,11) . '&quot; &gt;' . PROTOCOL . URL . substr($File,2) . '&lt;/a&gt;</td><td> ' . date ("Y-m-d H:i:s.", filemtime($File)) . '</td><td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $File . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
				if ($PageOrPlugin != 'Plugin')
				{
					echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $ReturnedPostID . '" /><input type="submit" class="btn btn-default btn-xs" name="DeleteSubmit" value="Delete" /></form></td>';
				}
				echo '</tr>';
			}
		}
	}
	
	function engine_media_plugin() //This handles the Media page plugin.
	{
		UI_media_page('Plugin');
	}
		
	function engine_analytics_page() //This handles the data for the analytics on the home page of The Back.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "SELECT ID,IP,Page,DateTime,Month FROM `" . DBPREFIX . "_AnalyticsTable` WHERE ID > 0 AND DateTime > CURDATE() - INTERVAL 1 YEAR;";
		$DBResult = mysqli_query($DBConnection,$DBQuery);
		while($Row = mysqli_fetch_array($DBResult, MYSQLI_ASSOC))
		{
			$ID = mb_convert_encoding(htmlspecialchars($Row['ID']),"UTF-8");
			$IP = mb_convert_encoding(htmlspecialchars($Row['IP']),"UTF-8");
			$Page = mb_convert_encoding(htmlspecialchars($Row['Page']),"UTF-8");
			$Date = mb_convert_encoding(htmlspecialchars($Row['DateTime']),"UTF-8");
			$Month = mb_convert_encoding(htmlspecialchars($Row['Month']),"UTF-8");
			$Today = date("Y-m-d");
			if (substr($Date,0,10) == $Today)
			{
				$LastDayPages[$TotalDaysVisitors] = $Page;
				$LastDayVisitors[$TotalDaysVisitors] = $IP;
				$TotalDaysVisitors = $TotalDaysVisitors + 1;
			}
			if (strtotime($Date) > strtotime('-7 days'))
			{
				
				$LastWeekPages[$TotalWeeksVisitors] = $Page;
				$LastWeekVisitors[$TotalWeeksVisitors] = $IP;
				$TotalWeeksVisitors = $TotalWeeksVisitors + 1;
			}
			if (strtotime($Date) > strtotime('-30 days'))
			{
				
				$LastMonthPages[$TotalMonthsVisitors] = $Page;
				$LastMonthVisitors[$TotalMonthsVisitors] = $IP;
				$TotalMonthsVisitors = $TotalMonthsVisitors + 1;
			}
			if ($Month == 1)
			{
				$UsersInJan = $UsersInJan + 1;
			}
			else if ($Month == 2)
			{
				$UsersInFeb = $UsersInFeb + 1;
			}
			else if ($Month == 3)
			{
				$UsersInMar = $UsersInMar + 1;
			}
			else if ($Month == 4)
			{
				$UsersInApr = $UsersInApr + 1;
			}
			else if ($Month == 5)
			{
				$UsersInMay = $UsersInMay + 1;
			}
			else if ($Month == 6)
			{
				$UsersInJun = $UsersInJun + 1;
			}
			else if ($Month == 7)
			{
				$UsersInJul = $UsersInJul + 1;
			}
			else if ($Month == 8)
			{
				$UsersInAug = $UsersInAug + 1;
			}
			else if ($Month == 9)
			{
				$UsersInSep = $UsersInSep + 1;
			}
			else if ($Month == 10)
			{
				$UsersInOct = $UsersInOct + 1;
			}
			else if ($Month == 11)
			{
				$UsersInNov = $UsersInNov + 1;
			}
			else if ($Month == 12)
			{
				$UsersInDec = $UsersInDec + 1;
			}
			$TotalVisitors = $TotalVisitors + 1;
		}
		$UsersInJan = ($UsersInJan/$TotalVisitors)*100;
		$UsersInFeb = ($UsersInFeb/$TotalVisitors)*100;
		$UsersInMar = ($UsersInMar/$TotalVisitors)*100;
		$UsersInApr = ($UsersInApr/$TotalVisitors)*100;
		$UsersInMay = ($UsersInMay/$TotalVisitors)*100;
		$UsersInJun = ($UsersInJun/$TotalVisitors)*100;
		$UsersInJul = ($UsersInJul/$TotalVisitors)*100;
		$UsersInAug = ($UsersInAug/$TotalVisitors)*100;
		$UsersInSep = ($UsersInSep/$TotalVisitors)*100;
		$UsersInOct = ($UsersInOct/$TotalVisitors)*100;
		$UsersInNov = ($UsersInNov/$TotalVisitors)*100;
		$UsersInDec = ($UsersInDec/$TotalVisitors)*100; 
		UI_analytics_page($UsersInJan,$UsersInFeb,$UsersInMar,$UsersInApr,$UsersInMay,$UsersInJun,$UsersInJul,$UsersInAug,$UsersInSep,$UsersInOct,$UsersInNov,$UsersInDec,$TotalVisitors,$TotalDaysVisitors,$LastDayVisitors,$LastDayPages,$TotalWeeksVisitors,$LastWeekVisitors,$LastWeekPages,$TotalMonthsVisitors,$LastMonthVisitors,$LastMonthPages);
		mysqli_close($DBConnection);  
	}
	
	function UI_analytics_page($UsersInJan,$UsersInFeb,$UsersInMar,$UsersInApr,$UsersInMay,$UsersInJun,$UsersInJul,$UsersInAug,$UsersInSep,$UsersInOct,$UsersInNov,$UsersInDec,$TotalVisitors,$TotalDaysVisitors,$LastDayVisitors,$LastDayPages,$TotalWeeksVisitors,$LastWeekVisitors,$LastWeekPages,$TotalMonthsVisitors,$LastMonthVisitors,$LastMonthPages) //This handles the UI for the analytics on the home page of The Back.
	{
	?><div style="padding-top:1rem;">
	<style>
		.Month{display:block;text-overflow:clip;white-space:nowrap;overflow:hidden;float:left;}
	</style>
	<div class="container-fluid">
		<h1>Your Analytics</h1>
		<h2>Month-by-month breakdown over the last year.</h2>
	</div>
	<div style = "margin:0 auto 0 auto;width:80%;background-color:#EFEFEF;padding:0 0 0 0;">
		Total: <?php echo $TotalVisitors;?><br >
		<div style="background-color:#FFFF66;width:<?php echo $UsersInJan; ?>%;" class="Month"><p>Jan, <?php echo round($UsersInJan,3); ?>&#37;</p></div>
		<div style="background-color:#99FF33;width:<?php echo $UsersInFeb; ?>%;" class="Month"><p>Feb, <?php echo round($UsersInFeb,3); ?>&#37;</p></div>
		<div style="background-color:#FF9966;width:<?php echo $UsersInMar; ?>%;" class="Month"><p>Mar, <?php echo round($UsersInMar,3); ?>&#37;</p></div>
		<div style="background-color:#00FFFF;width:<?php echo $UsersInApr; ?>%;" class="Month"><p>Apr, <?php echo round($UsersInApr,3); ?>&#37;</p></div>
		<div style="background-color:#CC99FF;width:<?php echo $UsersInMay; ?>%;" class="Month"><p>May, <?php echo round($UsersInMay,3); ?>&#37;</p></div>
		<div style="background-color:#FF66CC;width:<?php echo $UsersInJun; ?>%;" class="Month"><p>Jun, <?php echo round($UsersInJun,3); ?>&#37;</p></div>
		<div style="background-color:#66FFCC;width:<?php echo $UsersInJul; ?>%;" class="Month"><p>Jul, <?php echo round($UsersInJul,3); ?>&#37;</p></div>
		<div style="background-color:#996633;width:<?php echo $UsersInAug; ?>%;" class="Month"><p>Aug, <?php echo round($UsersInAug,3); ?>&#37;</p></div>
		<div style="background-color:#FF6600;width:<?php echo $UsersInSep; ?>%;" class="Month"><p>Sep, <?php echo round($UsersInSep,3); ?>&#37;</p></div>
		<div style="background-color:#FF0000;width:<?php echo $UsersInOct; ?>%;" class="Month"><p>Oct, <?php echo round($UsersInOct,3); ?>&#37;</p></div>
		<div style="background-color:#993399;width:<?php echo $UsersInNov; ?>%;" class="Month"><p>Nov, <?php echo round($UsersInNov,3); ?>&#37;</p></div>
		<div style="background-color:#0000CC;width:<?php echo $UsersInDec; ?>%;" class="Month"><p>Dec, <?php echo round($UsersInDec,3); ?>&#37;</p></div>
	</div>
<div>
<br />
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<h2>Today&#39;s Stats</h1>
			<p>Number of visitors today: <?php echo $TotalDaysVisitors;?></p>
			<p>Pages visited today:</p>
			<pre><?php print_r(array_count_values($LastDayPages)); ?></pre>
			<p>IP Addresses of visitors today:</p>
			<pre><?php print_r(array_count_values($LastDayVisitors)); ?></pre>
		</div>
		<div class="col-xs-12 col-sm-4">
			<h2>Weekly Stats</h1>
			<p>Number of visitors over the last 7 days: <?php echo $TotalWeeksVisitors;?></p>
			<p>Pages visited over the last 7 days:</p>
			<pre><?php print_r(array_count_values($LastWeekPages)); ?></pre>
			<p>IP Addresses of visitors over the last 7 days:</p>
			<pre><?php print_r(array_count_values($LastWeekVisitors)); ?></pre>
		</div>
		<div class="col-xs-12 col-sm-4">
			<h2>Monthly Stats</h1>
			<p>Number of visitors over the last 30 days: <?php echo $TotalMonthsVisitors;?></p>
			<p>Pages visited over the last 30 days:</p>
			<pre><?php print_r(array_count_values($LastMonthPages)); ?></pre>
			<p>IP Addresses of visitors over the last 30 days:</p>
			<pre><?php print_r(array_count_values($LastMonthVisitors)); ?></pre>
		</div>
	</div>
</div><?php
	}
	
	function engine_register_page() //This handles the data for the Register page.
	{
		if (isset($_POST['RegisterSubmit']))
		{
			$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
			if (!$DBConnection)
			{
				die('Could not connect to database.  Please try again later.');
			}
			if(isset($_POST['Username']) && isset($_POST['Email']) && !empty($_POST['Username']) && !empty($_POST['Email']) && isset($_POST['Password1']) && !empty($_POST['Password1']) && isset($_POST['Password2']) && !empty($_POST['Password2']))
			{
				$SafeUsername = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Username']),"UTF-8"));
				$SafeEmail = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Email']),"UTF-8"));
				$SafePassword1 = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Password1']),"UTF-8"));
				$SafePassword2 = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Password2']),"UTF-8"));
				if (isset($_POST['Company']) && !empty($_POST['Company']))
				{
					$SafeCompany = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Company']),"UTF-8"));
				}
				if (isset($_POST['Website']) && !empty($_POST['Website']))
				{
					$SafeWebsite = mysqli_real_escape_string($DBConnection,mb_convert_encoding(htmlspecialchars($_POST['Website']),"UTF-8"));
				}
				if (isset($_POST['EmailIsPublic']) && !empty($_POST['EmailIsPublic']))
				{
					$SafeEmailIsPublic = 1;
				}
				else
				{
					$SafeEmailIsPublic = 0;
				}
				if ($SafePassword1 == $SafePassword2)
				{
					$SafePassword1 = password_hash($SafePassword1, PASSWORD_DEFAULT);
					if ($SafeEmailIsPublic == 1)
					{
						$DBQuery = "INSERT INTO `" . DBPREFIX . "_LoginTable` (Username,Password,Email,Company,URL,EmailIsPublic) VALUES ('" . $SafeUsername . "','" . $SafePassword1 . "','" . $SafeEmail . "','" . $SafeCompany . "','" . $SafeWebsite . "',1);";
						mysqli_query($DBConnection,$DBQuery);
						echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Account Added!</strong></p></div>';
					}
					else
					{
						$DBQuery = "INSERT INTO `" . DBPREFIX . "_LoginTable` (Username,Password,Email,Company,URL,EmailIsPublic) VALUES ('" . $SafeUsername . "','" . $SafePassword1 . "','" . $SafeEmail . "','" . $SafeCompany . "','" . $SafeWebsite . "',0);";
						mysqli_query($DBConnection,$DBQuery);
						echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Account Added!</strong></p></div>';
					}
				}
				else
				{
					echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>Passwords Do Not Match.</strong></p></div>';
				}
			}
			else
			{
				echo '<div class="row"><p class="col-xs-10 col-xs-push-1"><strong>You need at least username, password, and email address for an account.</strong></p></div>';
			}
			mysqli_close($DBConnection);
		}
		//Call in the UI, and pass variables to autofill the form
		UI_register_page();
	}

	function UI_register_page() //This handles the UI for the Register page.
	{
	?><div class="container-fluid">
	<div class="row">
		<form method="post" id="RegisterForm" class="form-horizontal col-xs-10 col-xs-push-1">
			<fieldset class="form-group">
				<legend>New Account:</legend>
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Username">Username*:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="Username" id="Username" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Password1">Password*:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="password" class="form-control" name="Password1" id="Password1" />
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-9 col-sm-push-3">
						<input type="password" class="form-control" name="Password2" id="Password2" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Company">Company:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="Company" id="Company" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="UserURL">Website:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="UserURL" id="UserURL" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="Email">Email*:</label> 
					<div class="col-xs-12 col-sm-9">
						<input type="text" class="form-control" name="Email" id="Email" />
					</div>
				</div>
				<br />
				<div class="row">
					<label class="control-label col-xs-12 col-sm-3" for="EmailPublic">Make email address public:</label> 
					<div class="col-xs-12 col-sm-9">
						<div class="checkbox">
							<input type="checkbox" name="EmailPublic" id="EmailPublic" />
						</div>
					</div>
				</div>
				<br />
				<div class="row">
					<input type="submit" class="btn btn-default col-xs-3" name="RegisterSubmit" id="RegisterSubmit" value="Add New Account" />
				</div>
			</fieldset>
		</form>
	</div>
</div><?php
	}
	
	function engine_logout_page($SafeCookie) //This handles the data for the Logout page.  It needs no UI.
	{
		$DBConnection = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
		if (!$DBConnection)
		{
			die('Could not connect to database.  Please try again later.');
		}
		$DBQuery = "UPDATE `" . DBPREFIX . "_LoginTable` SET Cookie = 'XXX' WHERE Cookie = '" . $SafeCookie . "';";
		mysqli_query($DBConnection,$DBQuery);
		mysqli_close($DBConnection);
		echo 'Logging You Out Now...  <script>window.location.href = "' . PROTOCOL . URL . '/Back/";</script>';
	}
?>
