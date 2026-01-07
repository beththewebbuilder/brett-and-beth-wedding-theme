<?php

function my_deregister_scripts(){
  wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'my_deregister_scripts' );

// Add thumbnails
add_theme_support('post-thumbnails');

//WordPress Titles
add_theme_support('title-tag');

//navigation menus
register_nav_menus(array(
  'primary' => __('Primary Menu'),
  'footer' => __('Footer Menu'),
));

add_action('wp_ajax_nopriv_bt_scf', 'bt_scf');
add_action('wp_ajax_save_bt_scf', 'bt_scf');

function styles_scripts_init() {
    //enqueue stylesheets
    wp_register_script('script', get_template_directory_uri() . '/script/ajax-script.js', array('jquery'),'',true);
    wp_localize_script('script','myajax',array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('script');
    
    wp_enqueue_style('style', get_template_directory_uri() . '/style.css');
}
add_action( 'wp_enqueue_scripts', 'styles_scripts_init' );

/**
 * Proper ob_end_flush() for all levels
 *
 * This replaces the WordPress `wp_ob_end_flush_all()` function
 * with a replacement that doesn't cause PHP notices.
 */
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
   while ( @ob_end_flush() );
} );

add_action('wp_ajax_save_rsvp', 'save_rsvp');
add_action('wp_ajax_nopriv_save_rsvp', 'save_rsvp');
function save_rsvp() {
  global $wpdb;

  if( $_POST['name'] != '') {
        try {
            $accept = true;
            $responseType = "accepted";
            if($_POST['response'] != 'yes') {
                $accept = false;
                $responseType = "declined";
            }
            $created_at = date('Y-m-d H:i:s');

            $wpdb->insert(
                'wp_rsvp_response',
                array(
                    'name' => $_POST['name'],
                    'people' => $_POST['people'],
                    'accept' => $accept,
                    'message' => $_POST['message'],
                    'song' => $_POST['song'],
                    'respond' => $created_at,
                )
            );

            //send email
            $to = "rsvp@brett-and-beth.co.uk";
            $subject = "Wedding RSVP - " . $responseType;
            $message = "<html>
            <head>
            <title>Wedding RSVP</title>
            </head>
            <body>
            <p>" . $_POST['name'] . " has " . $responseType . " the wedding invite!</p>
            <p>Go to <a href='brett-and-beth.co.uk/our-rsvp'>brett-and-beth.co.uk/our-rsvp</a> to see all responses.</p>
            </body>
            </html>";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <webmaster@brett-and-beth.co.uk>' . "\r\n";

            mail($to,$subject,$message, $headers);

            echo "Success";
        }
        catch (Exception $e) {
            echo "Error";
        }     
    }
    die();
}
 ?>
