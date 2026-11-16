<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'home';
$db = getDbConnection();
$newsList = [];

if ($db) {
    $res = $db->query("SELECT * FROM news ORDER BY published_at DESC LIMIT 6");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $newsList[] = $row;
        }
    }
}

// Fallback jika belum ada data berita di database
if (empty($newsList)) {
    $newsList = [
        [
            'news_id' => 1,
            'title' => 'Hearts2Hearts Announces Ticket Prices for HEARTS 2 HOUSE Fan Meeting in Jakarta',
            'tagline' => 'Headline',
            'content' => 'Official fan meeting tickets open next week with Early Bird and Regular categories. Don\'t miss the chance to meet the members in person!',
            'published_at' => '2026-06-08',
            'image' => 'assets/member_group/pg4.jpg'
        ],
        [
            'news_id' => 2,
            'title' => 'Summer Single Concept Photo Teaser Released!',
            'tagline' => 'Teaser',
            'content' => 'Hearts2Hearts has released a series of concept photos for an exciting new music project.',
            'published_at' => '2026-06-01',
            'image' => 'assets/cover_album/Flutter.jpg'
        ],
        [
            'news_id' => 3,
            'title' => 'Album "First Heartbeat" Surpasses 1 Million Streams',
            'tagline' => 'Milestone',
            'content' => 'Hearts2Hearts\' debut album reaches a global streaming milestone across digital music platforms.',
            'published_at' => '2026-05-28',
            'image' => 'assets/cover_album/The_Chase.jpg'
        ],
        [
            'news_id' => 4,
            'title' => 'Official Lightstick Ver. 2 Pre-Orders Open',
            'tagline' => 'Merchandise',
            'content' => 'The newly designed official lightstick featuring Bluetooth sync technology is now available for pre-order.',
            'published_at' => '2026-05-15',
            'image' => 'assets/product/light_stick.webp'
        ],
        [
            'news_id' => 5,
            'title' => 'Hearts2Hearts Wins "Best Rookie" Award',
            'tagline' => 'Achievement',
            'content' => 'The group takes home the prestigious Best Rookie award at the K-Pop Music Awards.',
            'published_at' => '2026-05-01',
            'image' => 'assets/cover_album/Focus.jpg'
        ],
        [
            'news_id' => 6,
            'title' => 'Summer Concert in Bali Announced',
            'tagline' => 'Concert',
            'content' => 'Hearts2Hearts announces a special open-air summer concert at GWK Cultural Park, Bali.',
            'published_at' => '2026-04-15',
            'image' => 'assets/cover_album/Blue_Moon.jpg'
        ]
    ];
}

$featuredNews = $newsList[0];
$gridNews = array_slice($newsList, 1, 3);
$extraNews = array_slice($newsList, 4);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hearts2Hearts — Official Website</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main>
    <!-- ============ HERO ============ -->
    <section class="hero">
      <img
        src="assets/member_group/home_photo.png"
        alt="Hearts2Hearts Group"
        class="hero-bg"
      />
      <div class="hero-overlay"></div>
      <div class="container">
        <div class="hero-content">
          <span class="hero-tagline">Your new favorite standard of love</span>
          <h1>HEARTS2HEARTS</h1>
          <p class="hero-sub">
            Hearts from all of us. Discover harmony, inspiration, and love in
            every note from our eight talented members.
          </p>
          <p class="hero-ask">♡ Are you new here?</p>
          <div class="hero-cta-row">
            <a href="about.php" class="btn btn-pink">Get to Know Us</a>
            <a href="music.php" class="btn-secondary-ghost">Stream Now &rarr;</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ NEWS ============ -->
    <section class="section-gray">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">News & Updates</span>
          <h2>Latest News</h2>
        </div>

        <!-- Featured News Item -->
        <div class="featured-layout" style="margin-bottom: 24px">
          <div style="background: var(--white); border-radius: var(--radius-sm); overflow: hidden;">
            <div style="height: 240px; background: var(--light-gray); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
              <img
                src="<?= h($featuredNews['image']) ?>"
                alt="<?= h($featuredNews['title']) ?>"
                style="width: 100%; height: 240px; object-fit: cover"
              />
            </div>
          </div>
          <div style="display: flex; flex-direction: column; justify-content: center; padding: 20px;">
            <span style="font-size: 12px; color: var(--secondary-pink); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
              <?= h($featuredNews['tagline'] ?: 'Headline') ?>
            </span>
            <h3><?= h($featuredNews['title']) ?></h3>
            <div class="news-excerpt">
              <p style="font-size: 14px">
                <?= h(mb_strimwidth($featuredNews['content'], 0, 180, '…')) ?>
              </p>
            </div>
            <a
              href="news.php?id=<?= h($featuredNews['news_id']) ?>"
              class="btn btn-pink btn-small"
              style="align-self: flex-start"
            >Read More</a>
          </div>
        </div>

        <!-- Grid 3 News -->
        <div class="grid-3">
          <?php foreach ($gridNews as $item): ?>
            <article class="card" style="padding: 0; border-radius: 8px; border: 1px solid var(--border-light); overflow: hidden;">
              <img
                src="<?= h($item['image']) ?>"
                alt="<?= h($item['title']) ?>"
                style="width: 100%; height: 140px; object-fit: cover"
              />
              <div style="padding: 16px">
                <time style="font-size: 12px; color: var(--secondary-pink); font-weight: 600; display: block; margin-bottom: 8px;">
                  <?= h(formatDateIndo($item['published_at'])) ?>
                </time>
                <h3><?= h($item['title']) ?></h3>
                <p style="font-size: 13px; margin: 0">
                  <?= h(mb_strimwidth($item['content'], 0, 100, '…')) ?>
                </p>
                <a
                  href="news.php?id=<?= h($item['news_id']) ?>"
                  class="btn btn-pink btn-small"
                  style="margin-top: 10px"
                >Read More</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <!-- Hidden extra news (revealed by "View All News") -->
        <?php if (!empty($extraNews)): ?>
          <div class="news-extra-wrapper" style="display: none">
            <div class="grid-3" style="margin-top: 24px">
              <?php foreach ($extraNews as $item): ?>
                <article class="card" style="padding: 0; border-radius: 8px; border: 1px solid var(--border-light); overflow: hidden;">
                  <img
                    src="<?= h($item['image']) ?>"
                    alt="<?= h($item['title']) ?>"
                    style="width: 100%; height: 140px; object-fit: cover"
                  />
                  <div style="padding: 16px">
                    <time style="font-size: 12px; color: var(--secondary-pink); font-weight: 600; display: block; margin-bottom: 8px;">
                      <?= h(formatDateIndo($item['published_at'])) ?>
                    </time>
                    <h3><?= h($item['title']) ?></h3>
                    <p style="font-size: 13px; margin: 0">
                      <?= h(mb_strimwidth($item['content'], 0, 100, '…')) ?>
                    </p>
                    <a
                      href="news.php?id=<?= h($item['news_id']) ?>"
                      class="btn btn-pink btn-small"
                      style="margin-top: 10px"
                    >Read More</a>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 24px">
          <a href="#" class="btn btn-pink" id="viewAllNewsBtn">View All News</a>
        </div>
      </div>
    </section>

    <!-- ============ FANCLUB ============ -->
    <section class="section-gray">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">Fan Club</span>
          <h2>Join "S2U"</h2>
          <p style="max-width: 600px; margin: 0 auto">
            Become part of the official fan community and enjoy exclusive
            benefits designed to enhance your Hearts2Hearts experience.
          </p>
        </div>
        <div class="benefit-grid">
          <div class="benefit-card">
            <span class="benefit-icon">🎫</span>
            <h3>Priority Ticket Access</h3>
            <p>
              Get early access to concert and event tickets 48 hours before
              general sale.
            </p>
          </div>
          <div class="benefit-card">
            <span class="benefit-icon">📱</span>
            <h3>Exclusive Content</h3>
            <p>
              Access behind-the-scenes content, daily member videos, and blogs
              not posted on public social media.
            </p>
          </div>
          <div class="benefit-card">
            <span class="benefit-icon">🛍️</span>
            <h3>Special Merchandise</h3>
            <p>
              Exclusive rights to purchase limited edition merchandise for
              fanclub members at the Official Store.
            </p>
          </div>
        </div>
        <div style="text-align: center; margin-top: 32px">
          <a
            href="https://chat.whatsapp.com/Jn7iG80BGJf8OErEK7Igfg?s=sw&p=a&mlu=3&amv=3"
            class="btn btn-pink"
            target="_blank"
          >Join Fanclub</a>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
