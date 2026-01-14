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

function safe($value) {
    $value = (string) $value;

    // Remove unwanted slashes added before submission
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    } else {
        // Defensive: also handle manually escaped input
        $value = str_replace('\\\'', '\'', $value);
        $value = str_replace('\\"', '"', $value);
    }

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function yesNoBadge($yes, $yesText = 'Attending', $noText = 'Declined') {
  $text = $yes ? "✔ $yesText" : "✖ $noText";
  $bg   = $yes ? '#E7F7EE' : '#FDECEC';
  $fg   = $yes ? '#1E7A3F' : '#A61B1B';
  return '<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:'.$bg.';color:'.$fg.';font-weight:600;font-size:13px;line-height:1;">'.$text.'</span>';
}

add_action('wp_ajax_save_rsvp', 'save_rsvp');
add_action('wp_ajax_nopriv_save_rsvp', 'save_rsvp');
function save_rsvp() {
  global $wpdb;
  $table = $wpdb->prefix . 'rsvp_response';

  if (!empty($_POST['name'])) {
    try {
      // --- Helpers ---
      $safe = function($v) {
        return htmlspecialchars(stripslashes((string)$v), ENT_QUOTES, 'UTF-8');
      };

      // --- Normalise inputs ---
      $rawName  = trim((string)($_POST['name'] ?? ''));   // for subject / logic
      $name    = sanitize_text_field($_POST['name'] ?? '');
      $people  = (int) ($_POST['people'] ?? 1);
      $answer  = sanitize_text_field($_POST['response'] ?? 'no'); // expects "yes" or "no"
      $accept  = ($answer === 'yes');
      $type    = $accept ? 'accepted' : 'declined';
      $created_at = current_time('mysql'); // WP timezone-safe

      $messageRaw = trim((string)($_POST['message'] ?? ''));
      $songRaw    = trim((string)($_POST['song'] ?? ''));

      // Optional logic: only store song if accepted
      $songToStore = $accept ? $songRaw : '';

      // --- Save to DB ---
      $wpdb->insert(
        $table,
        array(
          'name'    => $name,
          'people'  => $people,
          'accept'  => $accept ? 1 : 0,
          'message' => sanitize_textarea_field($messageRaw),
          'song'    => sanitize_text_field($songToStore),
          'respond' => $created_at,
        ),
        array('%s','%d','%d','%s','%s','%s')
      );

      // --- Email pieces ---
      $to = [
              'rsvp@brett-and-beth.co.uk',
              'bethany.c.luffman@gmail.com',
              'brettluffman@hotmail.co.uk',
            ];
      $subject = 'Wedding RSVP - ' . ucwords($rawName);

      $badge = function($yes, $yesText = 'Accepted', $noText = 'Declined') {
        $text = $yes ? "✔ $yesText" : "✖ $noText";
        $bg   = $yes ? '#E7F7EE' : '#FDECEC';
        $fg   = $yes ? '#1E7A3F' : '#A61B1B';
        return '<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:'.$bg.';color:'.$fg.';font-weight:600;font-size:13px;line-height:1;">'.$text.'</span>';
      };

      $messageBlock = ($messageRaw !== '')
        ? '<div style="margin-top:18px;">
            <div style="font-weight:700;color:#111;margin-bottom:6px;">Message</div>
            <div style="background:#F7F7F9;border:1px solid #eee;border-radius:10px;padding:12px;color:#333;font-size:14px;line-height:1.6;white-space:pre-wrap;">'
              . $safe($messageRaw) .
            '</div>
          </div>'
        : '';

      $songBlock = ($accept && $songRaw !== '')
        ? '<div style="margin-top:18px;">
            <div style="font-weight:700;color:#111;margin-bottom:6px;">Song request</div>
            <div style="color:#444;font-size:14px;line-height:1.5;">' . $safe($songRaw) . '</div>
          </div>'
        : '';

      // --- HTML email body ---
      $emailHtml = '
          <!doctype html>
          <html>
            <body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
                <tr>
                  <td align="center">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #eaeaea;border-radius:16px;overflow:hidden;">
                      <tr>
                        <td style="padding:22px 24px;border-bottom:1px solid #eee;">
                          <div style="font-size:20px;font-weight:800;color:#111;">Wedding RSVP</div>
                          <div style="margin-top:6px;color:#555;font-size:14px;">
                            RSVP from <strong style="color:#111;">'.$safe($name).'</strong>
                            <span style="color:#777;">('.(int)$people.' '.((int)$people === 1 ? 'person' : 'people').')</span>
                          </div>
                        </td>
                      </tr>

                      <tr>
                        <td style="padding:18px 24px;">
                          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <div style="font-weight:800;color:#111;">Response</div>
                            <div>'.$badge($accept, 'Accepted', 'Declined').'</div>
                          </div>

                          '.$songBlock.'
                          '.$messageBlock.'
                        </td>
                      </tr>

                      <tr>
                        <td style="padding:14px 24px;border-top:1px solid #eee;color:#777;font-size:12px;">
                          Sent from your RSVP form. Tip: <a href="https://www.brett-and-beth.co.uk/our-rsvp">click here to view all responses.</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </body>
          </html>';

      // --- Headers (nice From name in Gmail) ---
      $fromEmail = 'webmaster@brett-and-beth.co.uk';
      $fromName  = 'Wedding RSVP';

      $headers   = array(
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $fromName . ' <' . $fromEmail . '>',
      );

      // Send using WP mailer
      $sent = wp_mail($to, $subject, $emailHtml, $headers);

      if (!$sent) {
        error_log('Party RSVP email failed to send for: ' . $name);
      }

      wp_send_json_success(['id' => $wpdb->insert_id]);

    } catch (Exception $e) {
      wp_send_json_error([
        'message'   => 'RSVP save failed',
        'db_error'  => $wpdb->last_error ?? null,
        'exception' => $e->getMessage(),
      ], 500);
    }
  } else {
    wp_send_json_error(['message' => 'Name missing'], 400);
  }

  wp_die();
}


add_action('wp_ajax_save_weekend_rsvp', 'save_weekend_rsvp');
add_action('wp_ajax_nopriv_save_weekend_rsvp', 'save_weekend_rsvp');

function save_weekend_rsvp() {
    global $wpdb;
    $table = $wpdb->prefix . 'rsvp_weekend_response';


  if( $_POST['name'] != '') {
        try {
            $created_at = date('Y-m-d H:i:s');
            
            $rawName  = trim((string)($_POST['name'] ?? ''));   // for subject / logic
            $name              = safe($_POST['name'] ?? '');
            $acceptWeekend     = filter_var($_POST['acceptWeekend'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $acceptParty       = filter_var($_POST['acceptParty'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $stayBothNights    = $acceptWeekend
                                    ? filter_var($_POST['stayBothNights'] ?? false, FILTER_VALIDATE_BOOLEAN)
                                    : false;
            $stayDetails       = trim((string)($_POST['stayDetails'] ?? ''));
            $dietaryReq        = $acceptWeekend ? filter_var($_POST['dietaryRequirements'] ?? false, FILTER_VALIDATE_BOOLEAN) : false;
            $dietaryDetails    = trim((string)($_POST['dietaryDetails'] ?? ''));
            $song              = trim((string)($_POST['song'] ?? ''));
            $message           = trim((string)($_POST['message'] ?? ''));

            
            $inserted = $wpdb->insert(
                $table,
                array(
                    'name' => remove_emojis(sanitize_text_field($_POST['name'] ?? '')),
                    'accept_weekend' => $acceptWeekend,
                    'accept_party' => $acceptParty,
                    'stay_both_nights' => $stayBothNights,
                    'not_staying_details'  => ($acceptWeekend && !$stayBothNights) ? remove_emojis(sanitize_text_field($_POST['stayDetails'] ?? '')) : '',
                    'dietary_requirements' => $dietaryReq,
                    'dietary_details'      => $dietaryReq ? remove_emojis(sanitize_text_field($_POST['dietaryDetails'] ?? '')) : '',
                    'song'                 => $acceptParty ? remove_emojis(sanitize_text_field($_POST['song'] ?? '')) : '',
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
            /* ---------- Wedding Weekend section ---------- */
            $accommodationRows = $acceptWeekend
            ? (
                $stayBothNights
                    ? '<tr>
                        <td style="padding:10px 0;border-top:1px solid #eee;">Accommodation</td>
                        <td style="padding:10px 0;border-top:1px solid #eee;text-align:right;">'.yesNoBadge(true, 'Staying both nights', 'Not staying both nights').'</td>
                      </tr>'
                    : '<tr>
                        <td style="padding:10px 0;border-top:1px solid #eee;">Accommodation</td>
                        <td style="padding:10px 0;border-top:1px solid #eee;text-align:right;">'.yesNoBadge(false, 'Staying both nights', 'Not staying both nights').'</td>
                      </tr>'
                      . (!empty($stayDetails)
                            ? '<tr>
                                <td colspan="2" style="padding:10px 0;color:#666;font-size:14px;">
                                  <strong>Stay details:</strong> '.safe($stayDetails).'
                                </td>
                              </tr>'
                            : ''
                        )
              )
            : '';
            
            $dietaryBlock = $acceptWeekend
            ? (
                $dietaryReq
                    ? '<tr>
                      <td colspan="2" style="padding:10px 0;border-top:1px solid #eee;">
                        <strong>Dietary requirements:</strong><br>
                        <span style="color:#555;font-size:14px;">'
                          . (!empty($dietaryDetails) ? safe($dietaryDetails) : '<em>No extra details provided.</em>')
                        . '</span>
                      </td>
                    </tr>'
                    : ''
                ) 
            : '';
            
            /* ---------- Party section ---------- */
            
            $songRow = ($acceptParty && !empty($song))
              ? '<tr>
                  <td colspan="2" style="padding:10px 0;border-top:1px solid #eee;">
                    <strong>Favourite dance song:</strong><br>
                    <span style="color:#555;font-size:14px;">'.safe($song).'</span>
                  </td>
                </tr>'
              : '';
            
            /* ---------- Message ---------- */
            
            $messageBlock = !empty($message)
              ? '<div style="margin-top:18px;">
                  <div style="font-weight:700;margin-bottom:6px;">Message</div>
                  <div style="background:#F7F7F9;border:1px solid #eee;border-radius:10px;padding:12px;font-size:14px;line-height:1.6;white-space:pre-wrap;">'
                    . safe($message) .
                  '</div>
                </div>'
              : '';
            
            $to = [
              'rsvp@brett-and-beth.co.uk',
              'bethany.c.luffman@gmail.com',
              'brettluffman@hotmail.co.uk',
            ];
            $subject = "VIP RSVP " . ucwords($rawName);
            
            $emailHtml = '
            <!doctype html>
            <html>
            <body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0;">
            <tr>
            <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border:1px solid #eaeaea;border-radius:16px;">
            <tr>
            <td style="padding:22px 24px;border-bottom:1px solid #eee;">
              <div style="font-size:20px;font-weight:800;">Wedding Weekend RSVP</div>
              <div style="margin-top:6px;color:#555;font-size:14px;">
                You have had an RSVP from <strong>'.$name.'</strong>.
              </div>
            </td>
            </tr>
            
            <tr>
            <td style="padding:18px 24px;">
            
              <div style="font-weight:800;font-size:18px;margin-bottom:10px;">Wedding Weekend</div>
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:10px 0;">Wedding weekend</td>
                  <td style="padding:10px 0;text-align:right;">'.yesNoBadge($acceptWeekend).'</td>
                </tr>
                '.$accommodationRows.'
                '.$dietaryBlock.'
              </table>
              <hr/>
            
              <div style="font-weight:800;font-size:18px;margin:20px 0 10px;">Happily Ever After Party</div>
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:10px 0;">Party attendance</td>
                  <td style="padding:10px 0;text-align:right;">'.yesNoBadge($acceptParty).'</td>
                </tr>
                '.$songRow.'
              </table>
            
              '.$messageBlock.'
            
            </td>
            </tr>
            
            <tr>
            <td style="padding:14px 24px;border-top:1px solid #eee;color:#777;font-size:12px;">
              Sent from your RSVP form. Tip: <a href="https://www.brett-and-beth.co.uk/our-rsvp">click here to view all responses.</a>
            </td>
            </tr>
            
            </table>
            </td>
            </tr>
            </table>
            </body>
            </html>';

            $fromEmail = 'rsvp@brett-and-beth.co.uk';
            $fromName  = 'Wedding RSVP'; // shows in Gmail as the sender name
            
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $fromName . " <" . $fromEmail . ">\r\n";

            $sent = wp_mail($to, $subject, $emailHtml, $headers);
            error_log('RSVP email sent? ' . ($sent ? 'yes' : 'no'));
            
            global $phpmailer;
            if (!$sent && isset($phpmailer)) {
                error_log('Mailer error: ' . $phpmailer->ErrorInfo);
            }


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
