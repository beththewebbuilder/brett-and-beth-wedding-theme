<!DOCTYPE html>
<html lang="en">
  <head>
    <title>
      <?php 
        if(!is_home()) {
          wp_title(''); 
          echo ' | ';
        }
      ?>
      <?php echo get_bloginfo('name'); ?>
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="<?php get_bloginfo('description'); ?>">

    <link rel="icon" href="<?php echo get_bloginfo('template_directory'); ?>/assets/favicon.png">
    <?php wp_head(); ?>

    <link rel="apple-touch-icon" href="<?php echo get_bloginfo('template_directory'); ?>/assets/favicon.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_bloginfo('template_directory'); ?>/assets/favicon.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_bloginfo('template_directory'); ?>/assets/favicon.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_bloginfo('template_directory'); ?>/assets/favicon.png" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tsparticles/confetti@3.0.3/tsparticles.confetti.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/f8042aa37c.js" crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bacasime+Antique&family=Inria+Serif:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=WindSong:wght@400;500&display=swap" rel="stylesheet">
  
    </head>

  <body>