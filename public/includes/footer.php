<?php
/**
 * Pentagon Safaris — Shared Footer Component
 */
use App\Services\HomeMediaService;
use App\Services\SiteSettingsService;

$base = $base ?? \App\Helpers\Path::baseUrl();
$subscribeAction = pq_url('handlers/index.php');
$footerLogo = (new HomeMediaService())->logoPath();
$socialLinks = (new SiteSettingsService())->getAll();

$footerSocials = [];
if (!empty($socialLinks['social_facebook'])) {
    $footerSocials[] = ['href' => $socialLinks['social_facebook'], 'label' => 'Facebook', 'icon' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'];
}
if (!empty($socialLinks['social_instagram'])) {
    $footerSocials[] = ['href' => $socialLinks['social_instagram'], 'label' => 'Instagram', 'icon' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>'];
}
if (!empty($socialLinks['social_tiktok'])) {
    $footerSocials[] = ['href' => $socialLinks['social_tiktok'], 'label' => 'TikTok', 'icon' => '<path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>'];
}
if (!empty($socialLinks['social_youtube'])) {
    $footerSocials[] = ['href' => $socialLinks['social_youtube'], 'label' => 'YouTube', 'icon' => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>'];
}
if (!empty($socialLinks['social_linkedin'])) {
    $footerSocials[] = ['href' => $socialLinks['social_linkedin'], 'label' => 'LinkedIn', 'icon' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>'];
}
if (!empty($socialLinks['social_whatsapp'])) {
    $waDigits = preg_replace('/\D+/', '', $socialLinks['social_whatsapp']);
    $footerSocials[] = ['href' => 'https://wa.me/' . $waDigits, 'label' => 'WhatsApp', 'icon' => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>'];
}
if (!empty($socialLinks['social_google_profile'])) {
    $footerSocials[] = ['href' => $socialLinks['social_google_profile'], 'label' => 'Google', 'icon' => '<circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/>'];
}
if (!empty($socialLinks['social_email'])) {
    $footerSocials[] = ['href' => 'mailto:' . $socialLinks['social_email'], 'label' => 'Email', 'icon' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/>'];
}
if (!empty($socialLinks['social_phone'])) {
    $phoneDigits = preg_replace('/\D+/', '', $socialLinks['social_phone']);
    $footerSocials[] = ['href' => 'tel:+' . $phoneDigits, 'label' => 'Call', 'icon' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>'];
}
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
        <img src="<?php echo $base . ltrim($footerLogo, '/'); ?>" alt="Logo" style="height: 40px; margin-bottom: 24px;">
        <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem; margin-bottom: 32px;">Crafting extraordinary African journeys since 2010. Experience the wild heart of the continent with our expert guides.</p>
        <div class="d-flex gap-3 flex-wrap">
          <?php foreach ($footerSocials as $social): ?>
          <a href="<?php echo htmlspecialchars($social['href']); ?>" title="<?php echo htmlspecialchars($social['label']); ?>" target="_blank" rel="noopener noreferrer" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; transition: var(--transition);" onmouseover="this.style.background='var(--gold)';this.style.borderColor='var(--gold)'" onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.1)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $social['icon']; ?></svg>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="col-lg-2 col-md-4">
        <h4 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">Explore</h4>
        <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; opacity: 0.7;">
          <li><a href="<?php echo pq_url(); ?>">Home</a></li>
          <li><a href="<?php echo pq_url('destinations'); ?>">Destinations</a></li>
          <li><a href="<?php echo pq_url('services'); ?>">What We Do</a></li>
          <li><a href="<?php echo pq_url('gallery'); ?>">Gallery</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-4">
        <h4 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">Support</h4>
        <ul class="list-unstyled" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; opacity: 0.7;">
          <li><a href="<?php echo pq_url('about'); ?>">Our Story</a></li>
          <li><a href="<?php echo pq_url('contact'); ?>">Contact Us</a></li>
          <li><a href="<?php echo pq_url('blog'); ?>">Blog</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-4">
        <h4 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">Newsletter</h4>
        <p style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 20px;">Get safari inspiration and exclusive offers.</p>
        <?php if (!empty($_GET['subscribed'])): ?>
          <p style="color: var(--gold); font-size: 0.9rem;">You are subscribed. Welcome to the pride.</p>
        <?php elseif (!empty($_GET['subscribe_error'])): ?>
          <p style="color: #ffb4b4; font-size: 0.9rem;">Please enter a valid email that is not already subscribed.</p>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($subscribeAction); ?>" style="display: flex; gap: 10px;">
          <?php echo pq_csrf_field(); ?>
          <input type="hidden" name="_action" value="subscribe">
          <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? $base); ?>">
          <input type="email" name="email" placeholder="Email address" required style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; padding: 10px 20px; color: #fff; outline: none;">
          <button type="submit" style="background: var(--gold); border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--charcoal);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </form>
      </div>
    </div>

    <div style="margin-top: 80px; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; font-size: 0.85rem; opacity: 0.5;">
      <div>&copy; <?php echo date('Y'); ?> Pentagon Safaris Tours &amp; Safaris. All rights reserved.</div>
      <div style="display: flex; gap: 24px;">
        <span>Nairobi, Kenya</span>
        <span>+254 718 620 982</span>
        <span>info@pentagonsafaris.com</span>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $base; ?>assets/js/main.js"></script>
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
