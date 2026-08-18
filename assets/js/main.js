/**
 * Pentagon Safaris — Main JS
 */
(function () {
  'use strict';

  /* ── Navbar scroll effect ── */
  const navbar = document.querySelector('.navbar-pq');
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 60) {
        navbar.classList.add('scrolled');
        navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.6)';
      } else {
        navbar.classList.remove('scrolled');
        navbar.style.boxShadow = '0 2px 12px rgba(0,0,0,0.5)';
      }
    });
  }

  /* ── Counter animation ── */
  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-target'), 10);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(function () {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = Math.floor(current).toLocaleString() + (el.getAttribute('data-suffix') || '');
    }, 16);
  }

  /* ── Intersection Observer for counters ── */
  const counters = document.querySelectorAll('[data-target]');
  if (counters.length) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(function (c) { observer.observe(c); });
  }

  /* ── Scroll reveal ── */
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length) {
    const revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(function (el) { revealObserver.observe(el); });
  }

  /* ── Contact form validation ── */
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const btn = contactForm.querySelector('[type="submit"]');
      btn.textContent = 'Sending...';
      btn.disabled = true;
      setTimeout(function () {
        const msg = document.getElementById('formSuccess');
        if (msg) {
          msg.style.display = 'block';
          contactForm.reset();
        }
        btn.textContent = 'Send Message';
        btn.disabled = false;
      }, 1500);
    });
  }

  /* ── Gallery lightbox (simple) ── */
  const galleryItems = document.querySelectorAll('.gallery-item');
  galleryItems.forEach(function (item) {
    item.addEventListener('click', function () {
      const title = item.getAttribute('data-title') || 'Gallery Image';
      // Simple modal trigger — Bootstrap modal
      const modalEl = document.getElementById('galleryModal');
      if (modalEl) {
        const modalTitle = modalEl.querySelector('.modal-title');
        const modalBody  = modalEl.querySelector('.modal-body');
        if (modalTitle) modalTitle.textContent = title;
        if (modalBody) {
          const cloned = item.querySelector('svg') ? item.querySelector('svg').cloneNode(true) : null;
          modalBody.innerHTML = '';
          if (cloned) { cloned.style.width = '100%'; cloned.style.height = '400px'; modalBody.appendChild(cloned); }
        }
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    });
  });

})();
