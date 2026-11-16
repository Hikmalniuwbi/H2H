<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'music';
$db = getDbConnection();

$albumsList = [];
if ($db) {
    $res = $db->query("
        SELECT a.album_id, a.album_title, a.release_date, a.album_type, a.cover_image,
               s.song_id, s.song_title, s.audio_file
        FROM albums a
        LEFT JOIN songs s ON a.album_id = s.album_id
        ORDER BY a.release_date DESC, a.album_id DESC
    ");
    if ($res) {
        $temp = [];
        while ($row = $res->fetch_assoc()) {
            $id = $row['album_id'];
            if (!isset($temp[$id])) {
                $temp[$id] = [
                    'album_id' => $row['album_id'],
                    'album_title' => $row['album_title'],
                    'release_date' => $row['release_date'],
                    'album_type' => $row['album_type'],
                    'cover_image' => $row['cover_image'],
                    'songs' => []
                ];
            }
            if ($row['song_id']) {
                $temp[$id]['songs'][] = [
                    'song_id' => $row['song_id'],
                    'song_title' => $row['song_title'],
                    'audio_file' => $row['audio_file']
                ];
            }
        }
        $albumsList = array_values($temp);
    }
}

// Fallback jika belum ada data album di database
if (empty($albumsList)) {
    $albumsList = [
        [
            'album_title' => 'The Chase',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/The_Chase.jpg',
            'songs' => [['song_title' => 'The Chase', 'audio_file' => 'assets/song/The_Chase.mp3']]
        ],
        [
            'album_title' => 'Style',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Style.jpg',
            'songs' => [['song_title' => 'Style', 'audio_file' => 'assets/song/Style.mp3']]
        ],
        [
            'album_title' => 'Focus',
            'album_type' => 'Mini Album',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Focus.jpg',
            'songs' => [['song_title' => 'Focus', 'audio_file' => 'assets/song/FOCUS.mp3']]
        ],
        [
            'album_title' => 'Pretty Please',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Pretty_Please.jpg',
            'songs' => [['song_title' => 'Pretty Please', 'audio_file' => 'assets/song/Pretty_Please.mp3']]
        ],
        [
            'album_title' => 'Focus (Remixes)',
            'album_type' => 'Remix Album',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Focus_Remix.jpg',
            'songs' => [['song_title' => 'Focus (Remix)', 'audio_file' => 'assets/song/Focus_Remix.mp3']]
        ],
        [
            'album_title' => 'Rude',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Rude.jpg',
            'songs' => [['song_title' => 'Rude', 'audio_file' => 'assets/song/Rude.mp3']]
        ],
        [
            'album_title' => 'Flutter',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Flutter.jpg',
            'songs' => [['song_title' => 'Flutter', 'audio_file' => 'assets/song/Flutter.mp3']]
        ],
        [
            'album_title' => 'Blue Moon',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Blue_Moon.jpg',
            'songs' => [['song_title' => 'Blue Moon', 'audio_file' => 'assets/song/Blue_Moon.mp3']]
        ],
        [
            'album_title' => 'Apple Pie',
            'album_type' => 'Single',
            'release_date' => '2025-01-01',
            'cover_image' => 'assets/cover_album/Apple_Pie.jpg',
            'songs' => [['song_title' => 'Apple Pie', 'audio_file' => 'assets/song/Apple_Pie.mp3']]
        ]
    ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Music — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Music</h1>
    <p>Explore every note and story from Hearts2Hearts.</p>
  </section>

  <main>
    <!-- VIDEO PLAYER -->
    <section class="section-white">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">Most Popular Video</span>
          <h2>Most Popular Music Video</h2>
          <p style="max-width: 600px; margin: 0 auto">
            Enjoy the most popular music video from Hearts2Hearts — Rude,
            viewed millions of times worldwide!
          </p>
        </div>
        <div class="video-container" style="aspect-ratio: 16 / 9; max-width: 560px">
          <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/F7sGJVUrkjQ"
            title="Hearts2Hearts Video"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </section>

    <!-- FULL DISCOGRAPHY -->
    <section class="section-blue">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle" style="color: rgba(255, 255, 255, 0.7)">Our Music</span>
          <h2 style="color: var(--white)">Music</h2>
        </div>
        <div class="grid-2">
          <?php foreach ($albumsList as $album): ?>
            <div class="album-card">
              <img
                src="<?= h($album['cover_image']) ?>"
                alt="<?= h($album['album_title']) ?>"
                class="album-cover"
              />
              <div class="album-info">
                <span class="album-type"><?= h($album['album_type']) ?></span>
                <h4><?= h($album['album_title']) ?></h4>
                <span class="album-date"><?= date('Y', strtotime($album['release_date'])) ?></span>

                <?php if (!empty($album['songs'])): ?>
                  <?php foreach ($album['songs'] as $s): ?>
                    <?php if (!empty($s['audio_file'])): ?>
                      <audio controls style="width: 100%; margin-top: 8px; height: 32px">
                        <source src="<?= h($s['audio_file']) ?>" type="audio/mpeg" />
                      </audio>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
