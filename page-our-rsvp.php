<?php
get_header();

global $wpdb;
$yesResponse = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response WHERE accept = 1" );
$noResponse = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response WHERE accept = 0" );
$totalResponse = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response" );
$allResponses = $wpdb->get_results( "SELECT * FROM wp_rsvp_response" );


$yesWeekendResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response WHERE accept_weekend = 1" );
$noWeekendResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response WHERE accept_weekend = 0" );
$yesWeekendPartyResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response WHERE accept_party = 1" );
$noWeekendPartyResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response WHERE accept_party = 0" );
$totalWeekendResponse = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response" );
$allWeekendResponses = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response" );

?>

<div class="floral-border-top">
    <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/floral-border-top.png"/>
</div>

<div class="names">
    <div class="baskerville-regular capital-text lg-font">
        Brett & Beth Weekend RSVPs
    </div>
    <p class="baskerville-regular">
        We have had a total of <strong><?php echo count($totalWeekendResponse);?></strong>
        responses from the <strong>12</strong> we sent out.
    </p>
</div>

</br>

<div class="results">
    <div class="yes-results">
        <div class="title">🥳</div>
        <div class="result-desc baskerville-regular">
            <strong>
                <?php echo count($yesWeekendResponse); ?>
            </strong>
            coming to our <strong>weekend</strong>!
        </div>
    </div>
    <div class="no-results">
        <div class="title">😔</div>
        <div class="result-desc baskerville-regular">
            <strong>
                <?php echo count($noWeekendResponse); ?>
            </strong>
            not coming to the <strong>weekend</strong>!
        </div>
    </div>
    <div class="yes-results">
        <div class="result-desc baskerville-regular">
            <strong>
                <?php echo count($yesWeekendPartyResponse); ?>
            </strong>
            coming to our <strong>party</strong>!
        </div>
    </div>
    <div class="no-results">
        <div class="result-desc baskerville-regular">
            <strong><?php echo count($noWeekendPartyResponse); ?></strong>
            not coming to the <strong>party</strong>!
        </div>
    </div>
</div>

<div class="table-list">
    <table class="inria-serif-regular">
        <thead>
            <tr>
                <th>Name</th>
                <th>Weekend</th>
                <th>Party</th>
                <th>Staying</th>
                <th>Staying details</th>
                <th>Dietary req.</th>
                <th>Dietary</th>
                <th>Song request</th>
                <th>Message</th>
                <th>RSVP</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allWeekendResponses as $rsvp) { ?>
                    <tr>
                        <td><?php echo $rsvp->name; ?></td>
                        <td><?php if($rsvp->accept_weekend == 1) echo '✔️'; if($rsvp->accept_weekend == 0) echo '❌'; ?></td>
                        <td><?php if($rsvp->accept_party == 1) echo '✔️'; if($rsvp->accept_party == 0) echo '❌'; ?></td>
                        <td><?php if($rsvp->stay_both_nights == 1) echo '✔️'; if($rsvp->stay_both_nights == 0) echo '❌'; ?></td>
                        <td><?php echo $rsvp->not_staying_details; ?></td>
                        <td><?php if($rsvp->dietary_requirements == 1) echo '✔️'; if($rsvp->dietary_requirements == 0) echo '❌'; ?></td>
                        <td><?php echo $rsvp->dietary_details; ?></td>
                        <td><?php echo $rsvp->song; ?></td>
                        <td><?php echo $rsvp->message; ?></td>
                        <td><?php echo (new DateTime($rsvp->respond))->format('d M H:i'); ?></td>

                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>


<div class="floral-page-split">
    <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/floral-page-split.png"/>
</div>

<div>
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
                <th>Accepted</th>
                <th>Song request</th>
                <th>Message</th>
                <th>RSVP</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allResponses as $rsvp) { ?>
                    <tr class="accept-<?php echo $rsvp->accept; ?>">
                        <td><?php echo $rsvp->name; ?></td>
                        <td><?php echo $rsvp->people; ?></td>
                        <td><?php if($rsvp->accept == 1) echo '✔️'; if($rsvp->accept == 0) echo '❌'; ?></td>
                        <td><?php echo $rsvp->Song; ?></td>
                        <td><?php echo $rsvp->message; ?></td>
                        <td><?php echo (new DateTime($rsvp->respond))->format('d M H:i'); ?></td>
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