// ================================================================
// Realty Smartz Pathshala - Production Client Bundle
// Bootstrap 5.3 Modules + Motion.dev (Self-hosted)
// Zero jQuery, zero heavy frameworks, accessible, < 50 KB gzipped
// ================================================================

import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Modal from 'bootstrap/js/dist/modal';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Tab from 'bootstrap/js/dist/tab';
import Toast from 'bootstrap/js/dist/toast';

import { animate, inView, scroll } from 'motion';

// Export Bootstrap instances globally for declarative usage
window.bootstrap = {
  Collapse,
  Dropdown,
  Modal,
  Offcanvas,
  Tab,
  Toast
};

document.addEventListener('DOMContentLoaded', () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // 1. Navbar Scroll Transformation (Transparent at top -> Centered Floating Pill on scroll)
  const header = document.getElementById('siteHeader');
  if (header) {
    let ticking = false;
    const update = () => {
      const y = window.scrollY || document.documentElement.scrollTop;
      if (y > 40) {
        header.classList.add('is-scrolled');
      } else if (y < 10) {
        header.classList.remove('is-scrolled');
      }
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(update);
      }
    }, { passive: true });

    update(); // Initial check on page load
  }

  // 2. Motion Reveal Animation for Sections & Elements
  if (!prefersReducedMotion) {
    const revealElements = document.querySelectorAll('[data-reveal]');
    revealElements.forEach(el => {
      // Set initial styles
      el.style.opacity = '0';
      el.style.transform = 'translateY(12px)';

      inView(el, () => {
        animate(
          el,
          { opacity: [0, 1], transform: ['translateY(12px)', 'translateY(0px)'] },
          { duration: 0.5, easing: [0.16, 1, 0.3, 1] }
        );
      });
    });
  }

  // 3. Dropdowns, Offcanvas and Tabs auto-initialization
  document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => new Dropdown(el));
  document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(el => new Offcanvas(el));
  document.querySelectorAll('[data-bs-toggle="tab"]').forEach(el => new Tab(el));
  document.querySelectorAll('[data-bs-toggle="modal"]').forEach(el => new Modal(el));

  // 4. Testimonials Slider Controls (Native scroll-by)
  const slider = document.getElementById('testimonialsSlider');
  const prevBtn = document.getElementById('sliderPrev');
  const nextBtn = document.getElementById('sliderNext');
  if (slider && prevBtn && nextBtn) {
    prevBtn.addEventListener('click', () => {
      slider.scrollBy({ left: -340, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
    });
    nextBtn.addEventListener('click', () => {
      slider.scrollBy({ left: 340, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
    });
  }
});
