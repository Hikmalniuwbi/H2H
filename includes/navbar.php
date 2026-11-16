<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$currentPage = $currentPage ?? '';
$user = currentUser();
?>
<header>
  <div class="nav-container">
    <div class="logo">
      <a href="index.php">
        <img src="assets/logo/logo.png" alt="Hearts2Hearts" class="logo-img" />
        Hearts2Hearts
      </a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
    <nav>
      <ul id="nav-menu">
        <li><a href="index.php" class="<?= ($currentPage === 'home') ? 'active' : '' ?>">Home</a></li>
        <li><a href="about.php" class="<?= ($currentPage === 'about') ? 'active' : '' ?>">About</a></li>
        <li><a href="music.php" class="<?= ($currentPage === 'music') ? 'active' : '' ?>">Music</a></li>
        <li><a href="store.php" class="<?= ($currentPage === 'store') ? 'active' : '' ?>">Store</a></li>
        <li><a href="events.php" class="<?= ($currentPage === 'events') ? 'active' : '' ?>">Events</a></li>
        <?php if (isLoggedIn()): ?>
          <li><a href="orders.php" class="<?= ($currentPage === 'orders') ? 'active' : '' ?>">My Orders</a></li>
          <li><a href="logout.php" style="color: var(--secondary-pink-dark); font-weight: 600;">Logout (<?= h($user['name'] ?? 'User') ?>)</a></li>
        <?php else: ?>
          <li><a href="login.php" class="<?= ($currentPage === 'login') ? 'active' : '' ?>">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
