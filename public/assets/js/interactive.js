/**
 * Realty Smartz Pathshala - Interactive Motion & Live Dynamic Animations
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Dynamic Text Rotator (Hero Headline)
  const rotatorEl = document.getElementById('heroRotatorText');
  if (rotatorEl) {
    const words = [
      'Real Estate Sales',
      'High-Ticket Closing',
      'Luxury Property Deals',
      'RERA & Documentation',
      'Negotiation Mastery',
      'Direct Developer Placement'
    ];
    let currentIndex = 0;

    setInterval(() => {
      rotatorEl.classList.add('fade-out');
      setTimeout(() => {
        currentIndex = (currentIndex + 1) % words.length;
        rotatorEl.textContent = words[currentIndex];
        rotatorEl.classList.remove('fade-out');
        rotatorEl.classList.add('fade-in');
        setTimeout(() => {
          rotatorEl.classList.remove('fade-in');
        }, 450);
      }, 350);
    }, 2800);
  }

  // 2. Animated Counter for Stat Numbers
  const statElements = document.querySelectorAll('.stat-value[data-counter]');
  if (statElements.length > 0 && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const targetNum = parseInt(el.getAttribute('data-counter'), 10) || 0;
          const suffix = el.getAttribute('data-suffix') || '';
          const duration = 1600; // ms
          const startTime = performance.now();

          const updateCount = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease-out cubic
            const easeOutProgress = 1 - Math.pow(1 - progress, 3);
            const currentNum = Math.floor(easeOutProgress * targetNum);

            el.textContent = currentNum + suffix;

            if (progress < 1) {
              requestAnimationFrame(updateCount);
            } else {
              el.textContent = targetNum + suffix;
            }
          };

          requestAnimationFrame(updateCount);
          obs.unobserve(el);
        }
      });
    }, { threshold: 0.3 });

    statElements.forEach(el => observer.observe(el));
  }

  // 3. Prevent any accidental horizontal scroll on mobile touch events
  window.addEventListener('resize', () => {
    if (document.documentElement.scrollWidth > window.innerWidth) {
      document.body.style.overflowX = 'hidden';
    }
  }, { passive: true });
});

