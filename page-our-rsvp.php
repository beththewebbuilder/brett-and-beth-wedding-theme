<?php
get_header();

global $wpdb;
$yesResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_response WHERE accept = 1" );
$noResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_response WHERE accept = 0" );
$allResponses = $wpdb->get_results( "SELECT * FROM wp_rsvp_response" );

?>

<div class="background-fade"></div>

<div class="results">
    <div class="yes-results">
        <div class="title">🥳</div>
        <div class="result-desc inria-serif-regular">We've got</div>
        <div class="result-num bacasime-antique-regular">
            <?php echo count($yesResponse); ?>
        </div>
        <div class="result-desc inria-serif-regular">coming to our party!</div>
    </div>
    <div class="no-results">
        <div class="title">😔</div>
        <div class="result-desc inria-serif-regular">There are</div>
        <div class="result-num bacasime-antique-regular">
            <?php echo count($noResponse); ?>
        </div>
        <div class="result-desc inria-serif-regular">losers not coming!</div>
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