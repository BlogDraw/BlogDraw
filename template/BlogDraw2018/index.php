<!-- 
	BlogDraw2018 Template for BlogDraw.  Copyright © TuxSoft Limited 2018 - tuxsoft@tuxsoft.uk.
 -->
<!DOCTYPE html>
<html lang="en">
	<head>		
		<!-- Enable utf8 reading -->
		<meta charset="UTF-8" />
		<!-- Enable Mobile-first Optimization -->
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="MobileOptimized" content="400" />
		<meta name="HandheldFriendly" content="True" />
		<!-- Enable IE/Edge Standards mode -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- Use An Apple Touch Icon and Favicon-->
		<link rel="apple-touch-icon" href="<?php output_head_template_location(); ?>/apple-touch-icon.png" />
		<link rel="shortcut icon" href="<?php output_head_template_location(); ?>/favicon.ico" />
		<!-- Here`s where the SEO comes in. -->
		<meta name="description" content="<?php output_head_description(); ?>" />
		<title><?php output_head_title(); ?></title>
		<!-- Bootstrap -->
		<link rel="stylesheet" href="<?php output_head_template_location(); ?>/bootstrap-3.3.7-dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="<?php output_head_template_location(); ?>/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" />
		<link rel="stylesheet" href="<?php output_head_template_location(); ?>/style.css" />
	</head>
	<body>
		<nav class="navbar navbar-static-top navbar-default">
			<div class="container-fluid">
				<ul class="nav navbar-nav navbar-center">
					<li><a href="<?php output_home_link(); ?>" title="<?php echo TITLE; ?>">Home</a></li>
					<li><a href="<?php output_archive_link(); ?>" title="Archive | <?php echo TITLE; ?>">Archive</a></li>
					<li><a href="<?php output_contact_link(); ?>" title="Contact | <?php echo TITLE; ?>">Contact</a></li>
				</ul>
			</div>
		</nav>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12 jumbotron">
					<a class="no-effect" href="<?php output_home_link(); ?>" title="<?php echo TITLE; ?>"><?php output_site_title(true); ?></a>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-10 col-sm-push-1">
					<?php output_canonical_page();?>
				</div>
			</div>
		</div>
		<footer class="container-fluid">
			<hr />
			<div class="row">
				<div class="col-xs-12">
					<p>Content on <?php echo URL; ?>, Copyright &copy; <?php echo TITLE; ?> <?php echo date('Y'); ?></p>
					<p><?php echo TITLE; ?>: Proudly powered by <a href="https://blogdraw.com">BlogDraw</a>.  Template: <?php echo TEMPLATE; ?> by <?php echo TEMPLATEBY; ?></p>
				</div>
			</div>
		</footer>
	</body>
</html>
<?php engine_analytics_collector(); ?>
