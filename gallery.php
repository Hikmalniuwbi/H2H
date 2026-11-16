<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'about';
$db = getDbConnection();

$galleryList = [];
if ($db) {
    $res = $db->query("SELECT g.*, m.name as member_name FROM gallery g LEFT JOIN members m ON g.member_id = m.member_id ORDER BY g.gallery_id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $galleryList[] = $row;
        }
    }
}

if (empty($galleryList)) {
    $galleryList = [
        ['image' => 'assets/member_group/pg1.jpg', 'caption' => 'Precious Moments 1', 'member_name' => 'Hearts2Hearts'],
        ['image' => 'assets/member_group/pg2.jpg', 'caption' => 'Precious Moments 2', 'member_name' => 'Hearts2Hearts'],
        ['image' => 'assets/member_group/pg3.jpg', 'caption' => 'Precious Moments 3', 'member_name' => 'Hearts2Hearts'],
        ['image' => 'assets/member_group/pg4.jpg', 'caption' => 'Precious Moments 4', 'member_name' => 'Hearts2Hearts'],
        ['image' => 'assets/member_group/pg5.jpg', 'caption' => 'Precious Moments 5', 'member_name' => 'Hearts2Hearts'],
        ['image' => 'assets/member_group/pg6.jpg', 'caption' => 'Precious Moments 6', 'member_name' => 'Hearts2Hearts'],
    ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gallery — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner">
    <h1>Our Gallery</h1>
    <p>Precious moments of Hearts2Hearts, our beloved journey together.</p>
  </section>

  <main>
    <section class="section-white">
      <div class="container">
        <div class="gallery-grid">
          <?php foreach ($galleryList as $g): ?>
            <div class="gallery-item">
              <img src="<?= h($g['image']) ?>" alt="<?= h($g['caption'] ?? 'Moment') ?>" />
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
