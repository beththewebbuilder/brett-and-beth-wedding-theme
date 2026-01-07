<?php
get_header();

global $wpdb;
$yesResponse = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response WHERE accept = 1" );
$noResponse = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response WHERE accept = 0" );
$totalResponse = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response" );
$allResponses = $wpdb->get_results( "SELECT * FROM wp_rsvp_response" );

?>

<div class="floral-border-top">
    <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/floral-border-top.png"/>
</div>

<div class="names">
    <div class="baskerville-regular capital-text lg-font">
        Brett & Beth Party RSVPs
    </div>
    <p class="baskerville-regular">
        We have had a total of <strong><?php echo $totalResponse;?></strong>
        responses from the <strong>255</strong> we sent out.
    </p>
</div>

</br>

<div class="results">
    <div class="yes-results">
        <div class="title">🥳</div>
        <div class="result-desc baskerville-regular">We've got</div>
        <div class="result-num baskerville-regular">
            <?php echo $yesResponse; ?>
        </div>
        <div class="result-desc baskerville-regular">coming to our party!</div>
    </div>
    <div class="no-results">
        <div class="title">😔</div>
        <div class="result-desc baskerville-regular">There are</div>
        <div class="result-num baskerville-regular">
            <?php echo $noResponse; ?>
        </div>
        <div class="result-desc baskerville-regular">losers not coming!</div>
    </div>
</div>

<div class="table-list">
    <table class="inria-serif-regular">
        <thead>
            <tr>
                <th>Name</th>
                <th>No. People</th>
                <th>RSVP</th>
                <th>Song request</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allResponses as $rsvp) { ?>
                    <tr class="accept-<?php echo $rsvp->accept; ?>">
                        <td><?php echo $rsvp->name; ?></td>
                        <td><?php echo $rsvp->people; ?></td>
                        <td><?php echo $rsvp->accept; ?></td>
                        <td><?php echo $rsvp->Song; ?></td>
                        <td><?php echo $rsvp->message; ?></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>


<?php
get_footer(); 
?>