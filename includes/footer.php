<footer>
  <div class="footer-container">
    <div class="footer-col">
      <div class="logo">
        <a href="index.php">
          <img src="assets/logo/logo.png" alt="Hearts2Hearts" class="logo-img" />
          Hearts2Hearts
        </a>
      </div>
      <p class="footer-desc">
        Hearts from all of us. Discover harmony and love in every note.
      </p>
    </div>
    <div class="footer-col">
      <h4>Legal</h4>
      <ul>
        <li><a href="#">Official Terms</a></li>
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Submit a Ticket</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="music.php">Music</a></li>
        <li><a href="store.php">Store</a></li>
        <li><a href="events.php">Events</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Company</h4>
      <ul>
        <li><a href="about.php">About Us</a></li>
        <li><a href="#">Fan Club</a></li>
        <li><a href="news.php">News</a></li>
      </ul>
    </div>
    <div class="footer-social">
      <a href="#" aria-label="Facebook"><img src="assets/svg/facebook.svg" alt="Facebook" /></a>
      <a href="#" aria-label="Instagram"><img src="assets/svg/instagram.svg" alt="Instagram" /></a>
      <a href="#" aria-label="Twitter"><img src="assets/svg/x.svg" alt="X" /></a>
      <a href="#" aria-label="TikTok"><img src="assets/svg/tiktok.svg" alt="TikTok" /></a>
      <a href="#" aria-label="YouTube"><img src="assets/svg/youtube.svg" alt="YouTube" /></a>
    </div>
  </div>
  <div class="footer-feedback">
    <h4>Send Your Feedback</h4>
    <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
      <?php $footerUser = currentUser(); ?>
      <form action="feedback.php" method="POST">
        <input type="text" name="name" value="<?= h($footerUser['name']) ?>" readonly style="background: rgba(255,255,255,0.15); color: #fff;" title="S2U Verified Account" />
        <input type="email" name="email" value="<?= h($footerUser['email']) ?>" readonly style="background: rgba(255,255,255,0.15); color: #fff;" title="S2U Verified Account" />
        <textarea name="message" placeholder="Your Message (S2U Fan)..." rows="3" required></textarea>
        <button type="submit">Send</button>
      </form>
    <?php else: ?>
      <form action="feedback.php" method="POST">
        <input type="text" name="name" placeholder="Your Name" />
        <input type="email" name="email" placeholder="Your Email" />
        <textarea name="message" placeholder="Your Message..." rows="3"></textarea>
        <button type="submit">Send</button>
      </form>
      <small style="display:block; margin-top:8px; font-size:12px; opacity:0.9;">
        Exclusive for registered S2U members. <a href="login.php?redirect=feedback.php&notice=feedback_login_required" style="color: #fff; text-decoration: underline; font-weight: 600;">Log In</a> / <a href="register.php?redirect=feedback.php&notice=feedback_login_required" style="color: #fff; text-decoration: underline; font-weight: 600;">Register</a>
      </small>
    <?php endif; ?>
  </div>
  <div class="footer-bottom">
    &copy; Copyright 2026 Hearts2Hearts Entertainment. All rights reserved.
  </div>
</footer>

<script>
  const hamburger = document.getElementById("hamburger");
  const navMenu = document.getElementById("nav-menu");
  if (hamburger && navMenu) {
    hamburger.addEventListener("click", () => {
      navMenu.classList.toggle("open");
    });
  }

  /* View All News reveal hidden articles */
  const viewAllBtn = document.getElementById("viewAllNewsBtn");
  if (viewAllBtn) {
    viewAllBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const wrapper = document.querySelector(".news-extra-wrapper");
      if (wrapper) {
        const isHidden = wrapper.style.display === "none";
        wrapper.style.display = isHidden ? "block" : "none";
        viewAllBtn.textContent = isHidden ? "Show Less" : "View All News";
      }
    });
  }
</script>
