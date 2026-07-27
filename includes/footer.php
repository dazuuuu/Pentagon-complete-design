<?php
/**
 * Pentagon Quest — Shared Footer Component (Modern Redesign)
 */
?>

<!-- Footer -->
<footer style="background: var(--charcoal); color: #fff; padding: 80px 0 40px; position: relative; overflow: hidden;">
  <!-- Subtle Ankara Background -->
  <div style="position: absolute; inset: 0; opacity: 0.03; pointer-events: none;">
    <svg width="100%" height="100%">
      <pattern id="footerPattern" width="100" height="100" patternUnits="userSpaceOnUse">
        <circle cx="50" cy="50" r="20" fill="none" stroke="#fff" stroke-width="1"/>
        <path d="M0,50 L100,50 M50,0 L50,100" stroke="#fff" stroke-width="0.5"/>
      </pattern>
      <rect width="100%" height="100%" fill="url(#footerPattern)"/>
    </svg>
  </div>

  <div class="container" style="position: relative; z-index: 1;">
    <div class="row g-5">
      <div class="col-lg-4">
        <img src="assets/images/logo.png" alt="Logo" style="height: 40px; filter: brightness(0) invert(1); margin-bottom: 24px;">
        <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem; margin-bottom: 32px;">Crafting extraordinary African journeys since 2010. Experience the wild heart of the continent with our expert guides.</p>
        <div class="d-flex gap-3">
          <a href="#" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; transition: var(--transition);" onmouseover="this.style.background='var(--gold)';this.style.borderColor='var(--gold)'" onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.1)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
          <a href="#" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; transition: var(--transition);" onmouseover="this.style.background='var(--gold)';this.style.borderColor='var(--gold)'" onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.1)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
        </div>
      </div>
      
      <div class="col-lg-2 col-md-4">
        <h4 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">Explore</h4>
        <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; opacity: 0.7;">
          <li><a href="index.php">Home</a></li>
          <li><a href="destinations.php">Destinations</a></li>
          <li><a href="services.php">What We Do</a></li>
          <li><a href="gallery.php">Gallery</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-4">
        <h4 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">Support</h4>
        <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; opacity: 0.7;">
          <li><a href="about.php">Our Story</a></li>
          <li><a href="contact.php">Contact Us</a></li>
          <li><a href="#">FAQs</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-4">
        <h4 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">Newsletter</h4>
        <p style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 20px;">Get safari inspiration and exclusive offers.</p>
        <?php if (isset($_GET['subscribed'])): ?>
        <p style="font-size: 0.85rem; color: var(--gold-soft); margin-bottom: 12px;">Thanks for subscribing!</p>
        <?php endif; ?>
        <?php if (isset($_GET['subscribe_error'])): ?>
        <p style="font-size: 0.85rem; color: #ffb4a9; margin-bottom: 12px;">Subscription failed. Please try again.</p>
        <?php endif; ?>
        <form method="POST" action="handlers/subscribe" style="display: flex; gap: 10px;">
          <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'index.php')); ?>">
          <input type="email" name="email" placeholder="Email address" required style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; padding: 10px 20px; color: #fff; outline: none;">
          <button type="submit" style="background: var(--gold); border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--charcoal);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </form>
      </div>
    </div>

    <div style="margin-top: 80px; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; font-size: 0.85rem; opacity: 0.5;">
      <div>&copy; <?php echo date('Y'); ?> Pentagon Quest Tours & Safaris. All rights reserved.</div>
      <div style="display: flex; gap: 24px;">
        <span>Nairobi, Kenya</span>
        <span>+254 700 000 000</span>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const nav = document.querySelector('.navbar-pq');
    if (nav) {
      if (window.scrollY > 50) {
        nav.classList.add('scrolled');
      } else {
        nav.classList.remove('scrolled');
      }
    }
  });

  // Scroll reveal animation
  const observerOptions = { threshold: 0.1 };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
      }
    });
  }, observerOptions);

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
