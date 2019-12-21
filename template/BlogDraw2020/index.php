<!--
  BlogDraw2020 Template for BlogDraw.  Copyright © TuxSoft Limited 2019 - tuxsoft@tuxsoft.uk.
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
    <link rel="apple-touch-icon" href="<?php output_home_link(); ?>/Uploads/apple-touch-icon.png" />
    <link rel="shortcut icon" href="<?php output_home_link(); ?>/Uploads/favicon.ico" />
    <!-- Here`s where the SEO comes in. -->
    <meta name="description" content="<?php output_head_description(); ?>" />
    <title><?php output_head_title(); ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php output_head_template_location(); ?>/bootstrap-4.4.1-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php output_head_template_location(); ?>/bootstrap-4.4.1-dist/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="<?php output_head_template_location(); ?>/style.css" />
  </head>
  <body>
    <nav class="navbar fixed-top navbar-dark bg-dark">
      <div class="container-fluid">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item"><a class="nav-link" href="<?php output_home_link(); ?>" title="<?php echo TITLE; ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php output_archive_link(); ?>" title="Archive | <?php echo TITLE; ?>">Archive</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php output_contact_link(); ?>" title="Contact | <?php echo TITLE; ?>">Contact</a></li>
        </ul>
      </div>
    </nav>
    <div class="jumbotron-fluid">
      <a class="text-decoration-none text-center" href="<?php output_home_link(); ?>" title="<?php echo TITLE; ?>">
        <?php output_site_title(true); ?>
      </a>
    </div>
    <div class="container-md">
      <div class="row">
        <div class="col-12 col-md-8">
          <?php output_canonical_page();?>
        </div>
        <div class="d-none d-md-4">
        <?php output_author_profile("Preamble"); ?>
          <aside class="card">
            <header class="card-header"><h3>Meet <?php output_author_profile("Caption"); ?>:</h3></header>
            <div class="card-body"><?php output_author_profile("Image"); ?><?php output_author_profile("Blurb"); ?></div>
          </aside>
        </div>
      </div>
    </div>
    <footer class="container-fluid">
      <hr />
      <div class="row">
        <div class="col-12">
          <p>Content on <?php echo URL; ?>, Copyright &copy; <?php echo TITLE; ?> <?php echo date('Y'); ?></p>
          <p><?php echo TITLE; ?>: Proudly powered by <a href="https://blogdraw.com">BlogDraw</a>.  Template: <?php echo TEMPLATE; ?> by <?php echo TEMPLATEBY; ?></p>
        </div>
      </div>
    </footer>
    <script src="<?php output_template_location(); ?>/bootstrap-4.4.1-dist/js/jquery-3.4.1.min.js"></script>
    <script src="<?php output_template_location(); ?>/bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
    <?php require_once ('./plugins/Cookies/index.php'); ?>
  </body>
</html>
