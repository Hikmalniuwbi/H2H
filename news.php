<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'home';
$db = getDbConnection();
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;
$allNews = [];

if ($db) {
    if ($articleId > 0) {
        $stmt = $db->prepare("SELECT * FROM news WHERE news_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $articleId);
            $stmt->execute();
            $article = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }
    $res = $db->query("SELECT * FROM news ORDER BY published_at DESC");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $allNews[] = $r;
        }
    }
}

// Fallback data
if (empty($allNews)) {
    $allNews = [
        [
            'news_id' => 1,
            'title' => 'Hearts2Hearts Announces Ticket Prices for HEARTS 2 HOUSE Fan Meeting in Jakarta',
            'tagline' => 'Headline',
            'content' => "Hearts2Hearts has officially released the seating layout and ticket prices for the HEARTS 2 HOUSE Fan Meeting in Jakarta!\n\nThe fan meeting is scheduled to take place at the Grand Ballroom, Jakarta International Expo, featuring special stages, acoustic sets, and exciting games with fans.\n\nTicket Categories:\n- VIP (Soundcheck + Send-off): Rp 1.500.000\n- Regular: Rp 750.000\n\nPresale for Official S2U Fanclub members opens on June 15, 2026, followed by General Sale on June 17, 2026. Make sure to prepare early and celebrate together with Hearts2Hearts!",
            'published_at' => '2026-06-08',
            'image' => 'assets/member_group/pg4.jpg'
        ],
        [
            'news_id' => 2,
            'title' => 'Summer Single Concept Photo Teaser Released!',
            'tagline' => 'Teaser',
            'content' => "Hearts2Hearts surprised fans at midnight with the release of vibrant and refreshing concept photos for their upcoming summer release!\n\nThe members pose against sunlit pastel beach backdrops, radiating joy and effortless summer vibes.",
            'published_at' => '2026-06-01',
            'image' => 'assets/cover_album/Flutter.jpg'
        ],
        [
            'news_id' => 3,
            'title' => 'Album "First Heartbeat" Surpasses 1 Million Streams',
            'tagline' => 'Milestone',
            'content' => "Hearts2Hearts debut album has officially surpassed one million streams across major streaming platforms within two weeks of release.\n\nThe members express heartfelt gratitude to S2U worldwide for this unforgettable milestone.",
            'published_at' => '2026-05-28',
            'image' => 'assets/cover_album/The_Chase.jpg'
        ],
        [
            'news_id' => 4,
            'title' => 'Official Lightstick Ver. 2 Pre-Orders Open',
            'tagline' => 'Merchandise',
            'content' => "The newly designed Hearts2Hearts Official Lightstick Ver. 2 is now available for pre-order at our Official Store!\n\nFeaturing Bluetooth central stage control and custom lighting effects, this lightstick is an essential companion for concerts and fan meetings.",
            'published_at' => '2026-05-15',
            'image' => 'assets/product/light_stick.webp'
        ],
        [
            'news_id' => 5,
            'title' => 'Hearts2Hearts Wins "Best Rookie" Award',
            'tagline' => 'Achievement',
            'content' => "Hearts2Hearts took home the prestigious Best Rookie of the Year trophy, delivering an emotional acceptance speech and dedicated the win to their fans.",
            'published_at' => '2026-05-01',
            'image' => 'assets/cover_album/Focus.jpg'
        ],
        [
            'news_id' => 6,
            'title' => 'Summer Concert in Bali Announced',
            'tagline' => 'Concert',
            'content' => "Get ready for an open-air concert under the stars! Hearts2Hearts will take the stage at GWK Cultural Park, Bali for a one-of-a-kind summer performance.",
            'published_at' => '2026-04-15',
            'image' => 'assets/cover_album/Blue_Moon.jpg'
        ]
    ];
}

if ($articleId > 0 && !$article) {
    foreach ($allNews as $n) {
        if ($n['news_id'] == $articleId) {
            $article = $n;
            break;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $article ? h($article['title']) : 'News' ?> — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner">
    <h1>News & Updates</h1>
    <p><a href="index.php" style="color:inherit;text-decoration:underline;">&larr; Back to Home</a></p>
  </section>

  <main>
    <section class="section-white">
      <div class="container" style="max-width: 800px;">
        <?php if ($article): ?>
          <article class="single-article">
            <span style="font-size: 13px; color: var(--secondary-pink); font-weight: 600; text-transform: uppercase;">
              <?= h($article['tagline'] ?: 'News') ?>
            </span>
            <h1 style="font-family: var(--font-heading); font-size: 32px; margin: 8px 0 12px 0;">
              <?= h($article['title']) ?>
            </h1>
            <p style="color: var(--text-light); font-size: 14px; margin-bottom: 24px;">
              Published on <?= h(formatDateIndo($article['published_at'])) ?>
            </p>

            <?php if (!empty($article['image'])): ?>
              <div style="margin-bottom: 30px; border-radius: 8px; overflow: hidden;">
                <img src="<?= h($article['image']) ?>" alt="<?= h($article['title']) ?>" style="width: 100%; max-height: 440px; object-fit: cover;" />
              </div>
            <?php endif; ?>

            <div style="font-size: 16px; line-height: 1.8; color: var(--body-color);">
              <p><?= nl2br(h($article['content'])) ?></p>
            </div>

            <div style="margin-top: 40px; border-top: 1px solid var(--border-light); padding-top: 20px;">
              <a href="news.php" class="btn btn-outline">&larr; Semua Berita</a>
            </div>
          </article>

        <?php else: ?>
          <div style="text-align: center; margin-bottom: 40px;">
            <h2>Semua Berita Terkini</h2>
            <p style="color: var(--text-light);">Ikuti perkembangan dan pengumuman resmi seputar Hearts2Hearts.</p>
          </div>

          <div class="grid-3">
            <?php foreach ($allNews as $item): ?>
              <article class="card" style="padding: 0; border-radius: 8px; border: 1px solid var(--border-light); overflow: hidden;">
                <img src="<?= h($item['image']) ?>" alt="<?= h($item['title']) ?>" style="width: 100%; height: 160px; object-fit: cover" />
                <div style="padding: 16px">
                  <time style="font-size: 12px; color: var(--secondary-pink); font-weight: 600; display: block; margin-bottom: 8px;">
                    <?= h(formatDateIndo($item['published_at'])) ?>
                  </time>
                  <h3 style="font-size: 16px; margin-bottom: 8px;"><?= h($item['title']) ?></h3>
                  <p style="font-size: 13px; margin: 0 0 12px 0; color: var(--body-color);">
                    <?= h(mb_strimwidth($item['content'], 0, 100, '…')) ?>
                  </p>
                  <a href="news.php?id=<?= h($item['news_id']) ?>" class="btn btn-pink btn-small">Read More</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
