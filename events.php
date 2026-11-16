<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'events';
$db = getDbConnection();
$eventsList = [];

if ($db) {
    $sql = "
        SELECT e.*, c.category_name, e.event_name AS title, e.ticket_stock AS quota, DATE_FORMAT(e.event_date, '%H:%i WIB') AS time 
        FROM events e 
        LEFT JOIN event_categories c ON e.category_id = c.category_id 
        ORDER BY e.event_date ASC
    ";
    $res = $db->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $eventsList[] = $row;
        }
    }
}

// Fallback jika belum ada data event di database
if (empty($eventsList)) {
    $eventsList = [
        [
            'event_id' => 1,
            'title' => 'HEARTS 2 HOUSE Fan Meeting in Jakarta',
            'event_date' => '2026-06-20',
            'time' => '19:00 WIB',
            'location' => 'Grand Ballroom, JIExpo Kemayoran, Jakarta',
            'price' => 750000,
            'quota' => 500,
            'ticket_link' => 'https://www.tiket.com',
            'category_name' => 'Fan Meeting'
        ],
        [
            'event_id' => 2,
            'title' => 'First Heartbeat Tour: Surabaya',
            'event_date' => '2026-07-05',
            'time' => '18:30 WIB',
            'location' => 'Grand City Convention Hall, Surabaya',
            'price' => 850000,
            'quota' => 350,
            'ticket_link' => 'https://www.tiket.com',
            'category_name' => 'Concert'
        ],
        [
            'event_id' => 3,
            'title' => 'Hearts2Hearts Summer Festival Live in Bali',
            'event_date' => '2026-07-18',
            'time' => '16:00 WITA',
            'location' => 'GWK Cultural Park, Bali',
            'price' => 600000,
            'quota' => 800,
            'ticket_link' => 'https://www.tiket.com',
            'category_name' => 'Festival'
        ],
        [
            'event_id' => 4,
            'title' => 'Focus Live in Bandung',
            'event_date' => '2026-08-02',
            'time' => '19:00 WIB',
            'location' => 'Eldorado Dome, Bandung',
            'price' => 550000,
            'quota' => 400,
            'ticket_link' => 'https://www.tiket.com',
            'category_name' => 'Concert'
        ]
    ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Events — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Upcoming Events</h1>
    <p>Catch Hearts2Hearts live on stage near you.</p>
  </section>

  <main>
    <!-- ============ UPCOMING EVENTS SCHEDULE ============ -->
    <section class="section-white">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">Live Schedule</span>
          <h2>2026 Tour & Events</h2>
          <p style="max-width: 600px; margin: 0 auto">
            Get your tickets early! Official S2U Fanclub members enjoy 48-hour
            priority access before general sale.
          </p>
        </div>

        <div style="overflow-x: auto">
          <table class="events-table">
            <thead>
              <tr>
                <th>Date & Time</th>
                <th>Event</th>
                <th>Category</th>
                <th>Location</th>
                <th>Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($eventsList as $ev): 
                $dateObj = new DateTime($ev['event_date']);
                $quota = (int)($ev['quota'] ?? 100);
              ?>
                <tr>
                  <td class="event-date-cell">
                    <strong><?= $dateObj->format('d M Y') ?></strong>
                    <br />
                    <span style="font-size: 13px; color: var(--text-light);"><?= h($ev['time'] ?? '19:00 WIB') ?></span>
                  </td>
                  <td>
                    <strong><?= h($ev['title']) ?></strong>
                  </td>
                  <td>
                    <span class="badge" style="background: var(--light-pink); color: var(--secondary-pink-dark); padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                      <?= h($ev['category_name'] ?? 'Concert') ?>
                    </span>
                  </td>
                  <td><?= h($ev['location']) ?></td>
                  <td style="font-weight: 600; color: var(--primary-blue);">
                    <?= formatRupiah($ev['price']) ?>
                  </td>
                  <td>
                    <?php 
                      $ticketUrl = !empty($ev['ticket_link']) ? $ev['ticket_link'] : 'https://www.tiket.com';
                      if ($quota > 0): 
                    ?>
                      <a href="<?= h($ticketUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-pink btn-small" title="Beli tiket di Tiket.com">Buy Ticket ↗</a>
                    <?php else: ?>
                      <button class="btn btn-outline btn-small" disabled style="opacity: 0.6; cursor: not-allowed;">Sold Out</button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ============ PAST EVENTS ============ -->
    <section class="section-gray">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">Memories</span>
          <h2>Past Events</h2>
        </div>
        <div class="grid-3">
          <div class="past-event-card">
            <div style="height: 200px; background: var(--light-gray); display: flex; align-items: center; justify-content: center; color: var(--text-light); font-size: 13px;">
              <img src="assets/member_group/pg1.jpg" alt="Debut Showcase 'First Heartbeat'" style="width:100%;height:200px;object-fit:cover;" />
            </div>
            <div class="past-event-body">
              <h4>Debut Showcase "First Heartbeat"</h4>
              <p>15 Jan 2025 — Jakarta</p>
            </div>
          </div>
          <div class="past-event-card">
            <div style="height: 200px; background: var(--light-gray); display: flex; align-items: center; justify-content: center; color: var(--text-light); font-size: 13px;">
              <img src="assets/member_group/pg2.jpg" alt="Mini Concert Focus" style="width:100%;height:200px;object-fit:cover;" />
            </div>
            <div class="past-event-body">
              <h4>Mini Concert "Focus"</h4>
              <p>12 Mar 2025 — Bandung</p>
            </div>
          </div>
          <div class="past-event-card">
            <div style="height: 200px; background: var(--light-gray); display: flex; align-items: center; justify-content: center; color: var(--text-light); font-size: 13px;">
              <img src="assets/member_group/pg3.jpg" alt="Fan Signing Event" style="width:100%;height:200px;object-fit:cover;" />
            </div>
            <div class="past-event-body">
              <h4>Fan Signing Event — Surabaya</h4>
              <p>05 May 2025 — Surabaya</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
