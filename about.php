<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'about';
$db = getDbConnection();

// Ambil data anggota dari database
$membersList = [];
if ($db) {
    $res = $db->query("SELECT * FROM members ORDER BY member_id ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $membersList[] = $row;
        }
    }
}

// Fallback jika database belum ada data members
if (empty($membersList)) {
    $membersList = [
        [
            'member_id' => 1,
            'name' => 'Jiwoo',
            'position' => 'Leader, Main Vocalist',
            'birth_date' => '2002-03-15',
            'height_cm' => 167,
            'blood_type' => 'O',
            'mbti' => 'ENFJ',
            'zodiac' => 'Pisces',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Jiwoo is a warm and dedicated leader. With strong vocals and captivating stage charisma, she is the main foundation of the group. Jiwoo is known for her ability to inspire other members and always prioritizes team unity. Off stage, Jiwoo is a caring figure often called the "mom" of Hearts2Hearts.',
            'fun_facts' => "Can cook 12 traditional Korean dishes\nHas a pet cat named \"Mochi\"\nFavorite color: pastel blue\nHobbies: painting, reading fantasy novels\nFavorite foods: tteokbokki and strawberries\nSpeaks 3 languages: Korean, English, Japanese",
            'profile_image' => 'assets/member_group/jiwoo.png'
        ],
        [
            'member_id' => 2,
            'name' => 'Ian',
            'position' => 'Lead Dancer, Sub Vocalist',
            'birth_date' => '2003-07-22',
            'height_cm' => 172,
            'blood_type' => 'A',
            'mbti' => 'ESTP',
            'zodiac' => 'Cancer',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Ian is a dance machine with explosive stage energy. His precise dance movements always become the highlight of every choreography. Behind the stage, he is a cheerful mood maker full of laughter.',
            'fun_facts' => "Can mimic dance moves after watching a video just once\nHas a sneaker collection of over 50 pairs\nFavorite colors: black and red\nHobbies: basketball, gaming, dance covers\nFavorite foods: samgyeopsal and pizza\nNickname: \"Ian Dance Machine\" from fans",
            'profile_image' => 'assets/member_group/ian.png'
        ],
        [
            'member_id' => 3,
            'name' => 'Carmen',
            'position' => 'Main Rapper, Lead Dancer',
            'birth_date' => '2002-01-10',
            'height_cm' => 165,
            'blood_type' => 'B',
            'mbti' => 'ENTP',
            'zodiac' => 'Capricorn',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Carmen brings sharp flow and fierce stage presence. Her rap delivery adds an edgy flavor to Hearts2Hearts music while her bright personality shines off-stage.',
            'fun_facts' => "Writes her own rap verses\nLoves skateboarding\nFavorite drink: Iced Americano",
            'profile_image' => 'assets/member_group/carmen.png'
        ],
        [
            'member_id' => 4,
            'name' => 'Stella',
            'position' => 'Lead Vocalist, Visual',
            'birth_date' => '2003-11-04',
            'height_cm' => 168,
            'blood_type' => 'AB',
            'mbti' => 'ISFP',
            'zodiac' => 'Scorpio',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Stella captivates hearts with her crystal-clear vocals and elegant aura. Known for her gentle demeanor, she connects deeply with S2U through acoustic melodies.',
            'fun_facts' => "Plays acoustic guitar and piano\nLoves stargazing\nFavorite dessert: Strawberry shortcake",
            'profile_image' => 'assets/member_group/stella.png'
        ],
        [
            'member_id' => 5,
            'name' => 'Yuha',
            'position' => 'Main Dancer, Sub Rapper',
            'birth_date' => '2004-05-18',
            'height_cm' => 166,
            'blood_type' => 'O',
            'mbti' => 'ESFP',
            'zodiac' => 'Taurus',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Yuha is energetic and expressive on stage, commanding attention with sharp choreographic timing and infectious enthusiasm.',
            'fun_facts' => "Loves baking cookies for the members\nAlways carries hand sanitizer\nFavorite song to dance to: Rude",
            'profile_image' => 'assets/member_group/yuha.png'
        ],
        [
            'member_id' => 6,
            'name' => 'Yeon',
            'position' => 'Lead Vocalist, Center',
            'birth_date' => '2003-09-02',
            'height_cm' => 169,
            'blood_type' => 'A',
            'mbti' => 'INFJ',
            'zodiac' => 'Virgo',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Yeon balances vocal harmony and expressive stage center roles with quiet confidence and unmatched dedication.',
            'fun_facts' => "Night owl who loves writing journals\nFavorite season: Autumn\nCollects cute vintage postcards",
            'profile_image' => 'assets/member_group/yeon.png'
        ],
        [
            'member_id' => 7,
            'name' => 'Ana',
            'position' => 'Sub Vocalist, Maknae',
            'birth_date' => '2005-12-14',
            'height_cm' => 164,
            'blood_type' => 'O',
            'mbti' => 'ENFP',
            'zodiac' => 'Sagittarius',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Ana is the beloved youngest member who brings sunshine and infectious energy wherever she goes.',
            'fun_facts' => "Huge fan of anime\nFastest runner in the group\nFavorite snack: Gummy bears",
            'profile_image' => 'assets/member_group/ana.png'
        ],
        [
            'member_id' => 8,
            'name' => 'Juun',
            'position' => 'Lead Rapper, Sub Vocalist',
            'birth_date' => '2002-08-30',
            'height_cm' => 170,
            'blood_type' => 'B',
            'mbti' => 'INTP',
            'zodiac' => 'Virgo',
            'nationality' => 'Korea',
            'debut_year' => 2025,
            'about' => 'Juun brings cool sophistication and rhythm to every track, blending rap delivery with modern electronic beats.',
            'fun_facts' => "Composes beat tracks in his studio\nLoves vintage fashion\nCoffee enthusiast",
            'profile_image' => 'assets/member_group/juun.jpg'
        ]
    ];
}

// Ambil gallery dari database
$galleryList = [];
if ($db) {
    $resG = $db->query("SELECT * FROM gallery ORDER BY gallery_id DESC LIMIT 12");
    if ($resG) {
        while ($rg = $resG->fetch_assoc()) {
            $galleryList[] = $rg;
        }
    }
}
if (empty($galleryList)) {
    $galleryList = [
        ['image' => 'assets/member_group/pg1.jpg', 'caption' => 'Moment 1'],
        ['image' => 'assets/member_group/pg2.jpg', 'caption' => 'Moment 2'],
        ['image' => 'assets/member_group/pg3.jpg', 'caption' => 'Moment 3'],
        ['image' => 'assets/member_group/pg4.jpg', 'caption' => 'Moment 4'],
        ['image' => 'assets/member_group/pg5.jpg', 'caption' => 'Moment 5'],
        ['image' => 'assets/member_group/pg6.jpg', 'caption' => 'Moment 6'],
    ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner">
    <h1>About Hearts2Hearts</h1>
    <p>Discover our story, our vision, and our heart.</p>
  </section>

  <main>
    <section class="section-white">
      <div class="container grid-45-55">
        <div>
          <img src="assets/member_group/group photo.png" alt="Hearts2Hearts Group"
            style="width: 100%; height: auto; border-radius: 8px;" />
        </div>
        <div>
          <span class="section-subtitle">About Us</span>
          <h2>Hearts2Hearts</h2>
          <p class="hangeul-subtitle"
            style="font-family: var(--font-heading); font-size: 18px; color: var(--primary-blue); margin-bottom: 16px;">
            하츠투하츠
          </p>
          <p>
            Hearts2Hearts is a modern idol group of eight talented members
            focused on spreading happiness, motivation, and love through
            dynamic pop music harmony. Formed with the vision of bridging
            hearts between people, the group is committed to delivering
            the best stage performances at every opportunity.
          </p>
          <p>
            The name "Hearts2Hearts" symbolizes the genuine connection between
            members and fans — an emotional bridge created through music.
            Every song we release is a love letter to S2U around the world.
          </p>
          <p>
            Blending pop, R&B, and electronic dance genres,
            Hearts2Hearts continues to push creative boundaries to deliver
            music that not only sounds good but touches the soul.
          </p>
        </div>
      </div>
    </section>

    <!-- MEET THE MEMBERS -->
    <section class="section-gray">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">Our Hearts</span>
          <h2>Meet the Members</h2>
          <p style="max-width: 600px; margin: 0 auto">
            Click on a member photo to view their full profile.
          </p>
        </div>
        <div class="scroll-wrapper">
          <button class="scroll-btn scroll-left" onclick="scrollMembers('members-scroll', -340)"
            aria-label="Scroll left">
            <span>&lsaquo;</span>
          </button>
          <div class="horizontal-scroll" id="members-scroll">
            <?php foreach ($membersList as $idx => $m): ?>
              <div class="member-click-card" onclick="openMemberModal(<?= $idx ?>)">
                <img src="<?= h($m['profile_image'] ?: 'assets/member_group/home_photo.png') ?>" alt="<?= h($m['name']) ?>" />
                <span class="mcc-hint">Click for profile</span>
                <div class="mcc-overlay">
                  <div class="mcc-name"><?= h($m['name']) ?></div>
                  <div class="mcc-pos"><?= h($m['position']) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="scroll-btn scroll-right" onclick="scrollMembers('members-scroll', 340)"
            aria-label="Scroll right">
            <span>&rsaquo;</span>
          </button>
        </div>
      </div>
    </section>

    <!-- GALLERY -->
    <section class="section-white">
      <div class="container">
        <div style="text-align: center; margin-bottom: 40px">
          <span class="section-subtitle">Our Moment</span>
          <h2>Gallery</h2>
          <p style="max-width: 600px; margin: 0 auto">
            Precious moments of Hearts2Hearts, our beloved journey together
          </p>
        </div>
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

  <!-- MEMBER MODAL -->
  <div class="member-modal" id="memberModal">
    <div class="modal-backdrop" onclick="closeModal()"></div>
    <div class="modal-content">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <img class="modal-hero-img" id="modalImg" src="" alt="Member Photo" />
      <div class="modal-body">
        <h1 class="modal-name" id="modalName"></h1>
        <span class="modal-pos" id="modalPos"></span>

        <div class="modal-info-grid" id="modalInfo"></div>

        <div class="modal-desc" id="modalDesc"></div>

        <div class="modal-facts">
          <h3>Fun Facts</h3>
          <ul id="modalFacts"></ul>
        </div>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script>
    const membersData = <?= json_encode($membersList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    function scrollMembers(containerId, offset) {
      const container = document.getElementById(containerId);
      if (container) {
        container.scrollBy({ left: offset, behavior: 'smooth' });
      }
    }

    function openMemberModal(index) {
      const m = membersData[index];
      if (!m) return;

      document.getElementById('modalImg').src = m.profile_image || '';
      document.getElementById('modalImg').alt = m.name || '';
      document.getElementById('modalName').textContent = m.name || '';
      document.getElementById('modalPos').textContent = m.position || '';

      const infoContainer = document.getElementById('modalInfo');
      infoContainer.innerHTML = `
        <div class="modal-info-item"><span class="modal-info-label">Birth Date</span><span class="modal-info-val">${m.birth_date || '-'}</span></div>
        <div class="modal-info-item"><span class="modal-info-label">Height</span><span class="modal-info-val">${m.height_cm ? m.height_cm + ' cm' : '-'}</span></div>
        <div class="modal-info-item"><span class="modal-info-label">Blood Type</span><span class="modal-info-val">${m.blood_type || '-'}</span></div>
        <div class="modal-info-item"><span class="modal-info-label">MBTI</span><span class="modal-info-val">${m.mbti || '-'}</span></div>
        <div class="modal-info-item"><span class="modal-info-label">Zodiac</span><span class="modal-info-val">${m.zodiac || '-'}</span></div>
        <div class="modal-info-item"><span class="modal-info-label">Nationality</span><span class="modal-info-val">${m.nationality || '-'}</span></div>
        <div class="modal-info-item"><span class="modal-info-label">Debut Year</span><span class="modal-info-val">${m.debut_year || '2025'}</span></div>
      `;

      document.getElementById('modalDesc').innerHTML = `<p>${(m.about || '').replace(/\\n/g, '</p><p>')}</p>`;

      const factsList = document.getElementById('modalFacts');
      factsList.innerHTML = '';
      if (m.fun_facts) {
        const lines = m.fun_facts.split('\n');
        lines.forEach(line => {
          if (line.trim()) {
            const li = document.createElement('li');
            li.textContent = line.trim();
            factsList.appendChild(li);
          }
        });
      }

      document.getElementById('memberModal').classList.add('active');
    }

    function closeModal() {
      document.getElementById('memberModal').classList.remove('active');
    }
  </script>
</body>
</html>
