<?php
global $wpdb;
$yesPartyCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM wp_rsvp_response WHERE accept = 1");
$noPartyCount  = (int) $wpdb->get_var("SELECT COUNT(*) FROM wp_rsvp_response WHERE accept = 0");
$partyInvitees = 222;
$partyResponsesReceived = $wpdb->get_var( "SELECT SUM(people) FROM wp_rsvp_response" );
$allPartyResponses = $wpdb->get_results( "SELECT * FROM wp_rsvp_response" );
$partyRemaining = max(0, (int)$partyInvitees - $partyResponsesReceived);
$partyPct = ((int)$partyInvitees > 0) ? min(100, round(($partyResponsesReceived / (int)$partyInvitees) * 100)) : 0;


$yesWeekendCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM wp_rsvp_weekend_response WHERE accept_weekend = 1");
$noWeekendCount  = (int) $wpdb->get_var("SELECT COUNT(*) FROM wp_rsvp_weekend_response WHERE accept_weekend = 0");
$yesWeekendPartyCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM wp_rsvp_weekend_response WHERE accept_party = 1");
$noWeekendPartyCount  = (int) $wpdb->get_var("SELECT COUNT(*) FROM wp_rsvp_weekend_response WHERE accept_party = 0");
$weekendInvitesSent = 12;
$weekendResponsesReceived = (int) $wpdb->get_var( "SELECT COUNT(*) FROM wp_rsvp_weekend_response" );
$weekendRemaining = max(0, $weekendInvitesSent - $weekendResponsesReceived);
$allWeekendResponses = $wpdb->get_results( "SELECT * FROM wp_rsvp_weekend_response" );

function yn_icon($v) {
  return ((int)$v === 1) ? '✅' : '❌';
}
function h($v) {
  return esc_html((string)$v);
}

// ---- CSV export handler (Weekend RSVPs) ----
if (isset($_GET['export_weekend_csv'])) {
    // Fetch rows (order newest first)
    $rows = $wpdb->get_results("SELECT * FROM wp_rsvp_weekend_response ORDER BY respond DESC", ARRAY_A);

    // Output headers
    $filename = 'weekend-rsvps-' . date('Y-m-d_H-i') . '.csv';
    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // UTF-8 BOM helps Excel open it correctly
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');

    // Column headings (match your DB columns)
    fputcsv($out, [
        'Name',
        'Weekend',
        'Party',
        'Staying both nights',
        'Stay details',
        'Dietary requirements',
        'Dietary details',
        'Song',
        'Message',
        'Responded at'
    ]);

    foreach ($rows as $r) {
        fputcsv($out, [
            $r['name'] ?? '',
            ((int)($r['accept_weekend'] ?? 0) === 1) ? 'Yes' : 'No',
            ((int)($r['accept_party'] ?? 0) === 1) ? 'Yes' : 'No',
            ((int)($r['stay_both_nights'] ?? 0) === 1) ? 'Yes' : 'No',
            $r['not_staying_details'] ?? '',
            ((int)($r['dietary_requirements'] ?? 0) === 1) ? 'Yes' : 'No',
            $r['dietary_details'] ?? '',
            $r['song'] ?? '',
            $r['message'] ?? '',
            $r['respond'] ?? '',
        ]);
    }

    fclose($out);
    exit;
}

// ---- CSV export handler (Party RSVPs) ----
if (isset($_GET['export_party_csv'])) {
  $rows = $wpdb->get_results("SELECT * FROM wp_rsvp_response ORDER BY respond DESC", ARRAY_A);

  $filename = 'party-rsvps-' . date('Y-m-d_H-i') . '.csv';
  nocache_headers();
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $filename);
  header('Pragma: no-cache');
  header('Expires: 0');

  echo "\xEF\xBB\xBF";
  $out = fopen('php://output', 'w');

  fputcsv($out, ['Name','People','Accepted','Song','Message','Responded at']);

  foreach ($rows as $r) {
    fputcsv($out, [
      $r['name'] ?? '',
      $r['people'] ?? '',
      ((int)($r['accept'] ?? 0) === 1) ? 'Yes' : 'No',
      $r['Song'] ?? ($r['song'] ?? ''),
      $r['message'] ?? '',
      $r['respond'] ?? '',
    ]);
  }

  fclose($out);
  exit;
}

$export_url = add_query_arg(
    [
        'export_weekend_csv' => 1,
        '_wpnonce' => wp_create_nonce('export_weekend_rsvps')
    ],
    get_permalink()
);
$party_export_url = add_query_arg(
  [
    'export_party_csv' => 1,
    '_wpnonce' => wp_create_nonce('export_party_rsvps')
  ],
  get_permalink()
);

get_header();
?>

<div class="floral-border-top">
    <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/floral-border-top.png"/>
</div>

<div class="names">
    <div class="baskerville-regular capital-text lg-font">
        Brett & Beth Weekend RSVPs
    </div>
</div>

<div class="rsvp-dashboard">
  <!-- Wedding Weekend -->
  <div class="dash-card">
    <div class="dash-title baskerville-regular">Wedding Weekend</div>
    <div class="dash-split">
      <div class="dash-metric">
        <div class="dash-label">Yes</div>
        <div class="dash-value"><?php echo (int)$yesWeekendCount; ?></div>
      </div>
      <div class="dash-metric">
        <div class="dash-label">No</div>
        <div class="dash-value dash-muted"><?php echo (int)$noWeekendCount; ?></div>
      </div>
    </div>
  </div>

  <!-- Happily Ever After Party -->
  <div class="dash-card">
    <div class="dash-title baskerville-regular">Happily Ever After Party</div>
    <div class="dash-split">
      <div class="dash-metric">
        <div class="dash-label">Yes</div>
        <div class="dash-value"><?php echo (int)$yesWeekendPartyCount; ?></div>
      </div>
      <div class="dash-metric">
        <div class="dash-label">No</div>
        <div class="dash-value dash-muted"><?php echo (int)$noWeekendPartyCount; ?></div>
      </div>
    </div>
  </div>

  <!-- Progress / Responses -->
  <div class="dash-card dash-card-wide">
    <div class="dash-title baskerville-regular">Responses</div>

    <div class="dash-progress-row">
      <div class="dash-metric">
        <div class="dash-label">Received</div>
        <div class="dash-value"><?php echo $weekendResponsesReceived; ?></div>
      </div>

      <div class="dash-metric">
        <div class="dash-label">Sent</div>
        <div class="dash-value"><?php echo $weekendInvitesSent; ?></div>
      </div>

      <div class="dash-metric">
        <div class="dash-label">Remaining</div>
        <div class="dash-value dash-muted"><?php echo $weekendRemaining; ?></div>
      </div>
    </div>

    <div class="dash-bar" role="progressbar"
         aria-valuenow="<?php echo $weekendResponsesReceived; ?>"
         aria-valuemin="0"
         aria-valuemax="<?php echo $weekendInvitesSent; ?>">
      <?php
        $pct = ($weekendInvitesSent > 0) ? min(100, round(($weekendResponsesReceived / $weekendInvitesSent) * 100)) : 0;
      ?>
      <div class="dash-bar-fill" style="width: <?php echo $pct; ?>%;"></div>
    </div>

    <div class="dash-foot inria-serif-regular"><?php echo $pct; ?>% received</div>
  </div>
</div>



<div class="table-list">
    <div class="table-tools">
        <label class="table-search">
            <span class="sr-only">Search RSVPs</span>
            <input id="rsvpSearch" type="search" placeholder="Search name etc..." autocomplete="off">
        </label>

        <div style="display:flex; gap:12px; align-items:center;">
            <div id="rsvpCount" class="table-count"></div>
            <a class="btn-export" href="<?php echo esc_url($export_url); ?>">Export party CSV</a>
        </div>
    </div>
    <table id="weekendTable" class="inria-serif-regular rsvp-table">
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
          <?php foreach ($allWeekendResponses as $rsvp): ?>
            <tr class="row <?php echo ((int)$rsvp->accept_weekend === 1) ? 'is-yes' : 'is-no'; ?>">
              <td class="name"><?php echo h($rsvp->name); ?></td>
        
              <td class="yn"><?php echo yn_icon($rsvp->accept_weekend); ?></td>
              <td class="yn"><?php echo yn_icon($rsvp->accept_party); ?></td>
              <td class="yn"><?php echo yn_icon($rsvp->stay_both_nights); ?></td>
        
              <td class="notes">
                <?php $sd = trim((string)$rsvp->not_staying_details);
                if ($sd !== ''): ?>
                  <details><summary>View</summary><div class="details-box"><?php echo h($sd); ?></div></details>
                <?php endif; ?>
              </td>
        
              <td class="yn"><?php echo yn_icon($rsvp->dietary_requirements); ?></td>
        
              <td class="notes">
                <?php $dd = trim((string)$rsvp->dietary_details);
                if ($dd !== ''): ?>
                  <details><summary>View</summary><div class="details-box"><?php echo h($dd); ?></div></details>
                <?php endif; ?>
              </td>
        
              <td><?php echo h($rsvp->song); ?></td>
        
              <td class="notes">
                <?php $msg = trim((string)$rsvp->message);
                if ($msg !== ''): ?>
                  <details><summary>View</summary><div class="details-box"><?php echo nl2br(h($msg)); ?></div></details>
                <?php endif; ?>
              </td>
        
              <td class="date"><?php echo h((new DateTime($rsvp->respond))->format('d M H:i')); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>

    </table>
</div>


<div class="floral-page-split">
    <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/floral-page-split.png"/>
</div>

<div>
    <div class="baskerville-regular capital-text lg-font">
        Happily Ever After Party RSVPs
    </div>
</div>

<div class="rsvp-dashboard">
  <div class="dash-card">
    <div class="dash-title baskerville-regular">Party RSVPs</div>
    <div class="dash-split">
      <div class="dash-metric">
        <div class="dash-label">Yes</div>
        <div class="dash-value"><?php echo (int)$yesPartyCount; ?></div>
      </div>
      <div class="dash-metric">
        <div class="dash-label">No</div>
        <div class="dash-value dash-muted"><?php echo (int)$noPartyCount; ?></div>
      </div>
    </div>
  </div>

  <div class="dash-card dash-card-wide">
    <div class="dash-title baskerville-regular">Responses</div>

    <div class="dash-progress-row">
      <div class="dash-metric">
        <div class="dash-label">Received</div>
        <div class="dash-value"><?php echo $partyResponsesReceived; ?></div>
      </div>

      <div class="dash-metric">
        <div class="dash-label">Sent</div>
        <div class="dash-value"><?php echo (int)$partyInvitees; ?></div>
      </div>

      <div class="dash-metric">
        <div class="dash-label">Remaining</div>
        <div class="dash-value dash-muted"><?php echo $partyRemaining; ?></div>
      </div>
    </div>

    <div class="dash-bar" role="progressbar"
         aria-valuenow="<?php echo $partyResponsesReceived; ?>"
         aria-valuemin="0"
         aria-valuemax="<?php echo (int)$partyInvitees; ?>">
      <div class="dash-bar-fill" style="width: <?php echo $partyPct; ?>%;"></div>
    </div>

    <div class="dash-foot inria-serif-regular"><?php echo $partyPct; ?>% received</div>
  </div>
</div>

<div class="table-list">
    <div class="table-tools">
        <label class="table-search">
            <span class="sr-only">Search party RSVPs</span>
            <input id="partySearch" type="search" placeholder="Search name, message, song…" autocomplete="off">
        </label>

        <div style="display:flex; gap:12px; align-items:center;">
            <div id="partyCount" class="table-count"></div>
            <a class="btn-export" href="<?php echo esc_url($party_export_url); ?>">Export party CSV</a>
        </div>
    </div>
  <table id="partyTable" class="inria-serif-regular rsvp-table">
    <thead>
      <tr>
        <th>Name</th>
        <th class="yn">People</th>
        <th class="yn">Accepted</th>
        <th>Song request</th>
        <th>Message</th>
        <th class="date">RSVP</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($allPartyResponses as $rsvp): ?>
        <tr class="row accept-<?php echo (int)$rsvp->accept; ?>">
          <td class="name"><?php echo h($rsvp->name); ?></td>
          <td class="yn"><?php echo (int)$rsvp->people; ?></td>
          <td class="yn"><?php echo ((int)$rsvp->accept === 1) ? '✅' : '❌'; ?></td>

          <td><?php echo h($rsvp->Song ?? ''); ?></td>

          <td class="notes">
            <?php $msg = trim((string)($rsvp->message ?? ''));
            if ($msg !== ''): ?>
              <details>
                <summary>View</summary>
                <div class="details-box"><?php echo nl2br(h($msg)); ?></div>
              </details>
            <?php endif; ?>
          </td>

          <td class="date"><?php echo h((new DateTime($rsvp->respond))->format('d M H:i')); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
    // Search Weekend
(function () {
  const input = document.getElementById('rsvpSearch');
  const table = document.getElementById('weekendTable');
  if (!input || !table) return;

  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const countEl = document.getElementById('rsvpCount');

  // Cache row text for speed
  const rowText = rows.map(r => (r.innerText || r.textContent || '').toLowerCase());

  function updateCount(shown, total) {
    if (!countEl) return;
    countEl.textContent = `Showing ${shown} of ${total}`;
  }

  function filter() {
    const q = input.value.trim().toLowerCase();
    let shown = 0;

    rows.forEach((row, i) => {
      const match = q === '' || rowText[i].includes(q);
      row.style.display = match ? '' : 'none';
      if (match) shown++;
    });

    updateCount(shown, rows.length);
  }

  input.addEventListener('input', filter);
  filter(); // initial count
})();

// Search Party
(function () {
  const input = document.getElementById('partySearch');
  const table = document.getElementById('partyTable');
  const countEl = document.getElementById('partyCount');
  if (!input || !table) return;

  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const rowText = rows.map(r => (r.innerText || r.textContent || '').toLowerCase());

  function updateCount(shown, total) {
    if (!countEl) return;
    countEl.textContent = `Showing ${shown} of ${total}`;
  }

  function filter() {
    const q = input.value.trim().toLowerCase();
    let shown = 0;

    rows.forEach((row, i) => {
      const match = q === '' || rowText[i].includes(q);
      row.style.display = match ? '' : 'none';
      if (match) shown++;
    });

    updateCount(shown, rows.length);
  }

  input.addEventListener('input', filter);
  filter();
})();
</script>

<?php
get_footer(); 
?>