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

function remove_emojis($value) {
    $clean = wp_strip_all_tags($value);
    $clean = preg_replace('/[\x{1F000}-\x{1FFFF}]/u', '', $clean);
    return $clean;
}

add_action('wp_ajax_save_rsvp', 'save_rsvp');
add_action('wp_ajax_nopriv_save_rsvp', 'save_rsvp');
function save_rsvp() {
  global $wpdb;
  $table = $wpdb->prefix . 'rsvp_response';

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
                $table,
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
            <h2>Wedding RSVP</h2>
            <p>" . $_POST['name']  . "<em>(". $_POST['people'] . " people)</em> has " . $responseType . " the wedding invite!</p>
            <p><strong>Message:</strong> ". $_POST['message'] ."</p>
            <p>Go to <a href='brett-and-beth.co.uk/our-rsvp'>brett-and-beth.co.uk/our-rsvp</a> to see all responses.</p>
            </body>
            </html>";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <webmaster@brett-and-beth.co.uk>' . "\r\n";

            mail($to,$subject,$message, $headers);

            wp_send_json_success(['id' => $wpdb->insert_id]);
        }
        catch (Exception $e) {
            wp_send_json_error([
                'message'   => 'RSVP save failed',
                'db_error'  => $wpdb->last_error ?? null,
                'exception' => $e->getMessage(),
            ], 500);
        }     
    }
    else {
        wp_send_json_error(['message' => 'Name missing'], 400);
    }
    die();
}

add_action('wp_ajax_save_weekend_rsvp', 'save_weekend_rsvp');
add_action('wp_ajax_nopriv_save_weekend_rsvp', 'save_weekend_rsvp');

function save_weekend_rsvp() {
    global $wpdb;
    $table = $wpdb->prefix . 'rsvp_weekend_response';


  if( $_POST['name'] != '') {
        try {
            $created_at = date('Y-m-d H:i:s');
            
            $inserted = $wpdb->insert(
                $table,
                array(
                    'name' => remove_emojis(sanitize_text_field($_POST['name'] ?? '')),
                    'accept_weekend' => filter_var($_POST['acceptWeekend'], FILTER_VALIDATE_BOOLEAN),
                    'accept_party' => filter_var($_POST['acceptParty'], FILTER_VALIDATE_BOOLEAN),
                    'stay_both_nights' => filter_var($_POST['stayBothNights'], FILTER_VALIDATE_BOOLEAN),
                    'not_staying_details' => remove_emojis(sanitize_text_field($_POST['stayDetails'] ?? '')),
                    'dietary_requirements' => filter_var($_POST['dietaryRequirements'], FILTER_VALIDATE_BOOLEAN),
                    'dietary_details' => remove_emojis(sanitize_text_field($_POST['dietaryDetails'] ?? '')),
                    'song' => remove_emojis(sanitize_text_field($_POST['song'] ?? '')),
                    'message' => remove_emojis(sanitize_textarea_field($_POST['message'] ?? '')),
                    'respond' => $created_at,
                ),
                array(
                    '%s', // name
                    '%d', // accept_weekend
                    '%d', // accept_party
                    '%d', // stay_both_nights
                    '%s', // not_staying_details
                    '%d', // dietary_requirements
                    '%s', // dietary_details
                    '%s', // song
                    '%s', // message
                    '%s', // respond
                )
            );

            if ($inserted === false) {
                error_log('RSVP insert failed: ' . $wpdb->last_error);
                error_log('Last query: ' . $wpdb->last_query);
            } else {
                error_log('RSVP inserted, new ID: ' . $wpdb->insert_id);
            }

            //send email
            $to = "rsvp@brett-and-beth.co.uk";
            $subject = "VIP Wedding RSVP";
            $message = "<html>
            <head>
            <title>Wedding RSVP</title>
            </head>
            <body>
            <h2>Wedding RSVP</h2>
            <p>" . $_POST['name'] ." ". $_POST['acceptText'] . "!</p>
            <p><strong>Staying both nights:</strong> ". $_POST['stayBothNights'] . " <em>". $_POST['stayDetails'] ."</em></p>
            <p><strong>Message:</strong> ". $_POST['message'] ."</p>
            <p><strong>Dietary requirements:</strong> ". $_POST['dietary_requirements'] . ": " . $_POST['dietaryDetails'] ."</p>
            <p>Go to <a href='brett-and-beth.co.uk/our-rsvp'>brett-and-beth.co.uk/our-rsvp</a> to see all responses.</p>
            </body>
            </html>";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <webmaster@brett-and-beth.co.uk>' . "\r\n";

            mail($to,$subject,$message, $headers);

            wp_send_json_success(['id' => $wpdb->insert_id]);
        }
        catch (Exception $e) {
            wp_send_json_error([
                'message'   => 'RSVP save failed',
                'db_error'  => $wpdb->last_error ?? null,
                'exception' => $e->getMessage(),
            ], 500);
        }     
    }
    else {
        wp_send_json_error(['message' => 'Name missing'], 400);
    }
    die();
}
 ?>
