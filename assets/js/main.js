/**
 * Roppets Edutainment Production Inc. - Main Script
 * Interactive UI/UX Logic, CMS Connectors & Real-Time Availability
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Hero Slider Logic
  const slides = document.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.dot');
  const prevBtn = document.querySelector('.slider-arrow.prev');
  const nextBtn = document.querySelector('.slider-arrow.next');
  let currentSlide = 0;
  let slideInterval;

  function showSlide(index) {
    if (!slides.length) return;
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === index);
    });
    currentSlide = index;
  }

  function nextSlide() {
    let next = (currentSlide + 1) % slides.length;
    showSlide(next);
  }

  function prevSlide() {
    let prev = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(prev);
  }

  if (slides.length > 0) {
    function startSlider() {
      slideInterval = setInterval(nextSlide, 5500);
    }
    function stopSlider() {
      clearInterval(slideInterval);
    }

    startSlider();

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        nextSlide();
        stopSlider();
        startSlider();
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        prevSlide();
        stopSlider();
        startSlider();
      });
    }

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => {
        showSlide(idx);
        stopSlider();
        startSlider();
      });
    });

    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
      sliderContainer.addEventListener('mouseenter', stopSlider);
      sliderContainer.addEventListener('mouseleave', startSlider);
    }
  }

  // 2. Mobile Menu Toggle
  const mobileToggle = document.querySelector('.mobile-toggle');
  const mainNav = document.querySelector('.main-nav');
  if (mobileToggle && mainNav) {
    mobileToggle.addEventListener('click', () => {
      mainNav.classList.toggle('open');
      const icon = mobileToggle.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-xmark');
      }
    });

    // Mobile dropdown toggle
    const navItemsWithDropdown = document.querySelectorAll('.nav-item.has-dropdown');
    navItemsWithDropdown.forEach(item => {
      const link = item.querySelector('.nav-link');
      if (link && window.innerWidth <= 768) {
        link.addEventListener('click', (e) => {
          if (window.innerWidth <= 768) {
            e.preventDefault();
            item.classList.toggle('open');
          }
        });
      }
    });
  }

  // 3. Service Category Filtering
  const tabBtns = document.querySelectorAll('.tab-btn');
  const serviceCards = document.querySelectorAll('.service-card');

  if (tabBtns.length && serviceCards.length) {
    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        tabBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.getAttribute('data-filter');

        serviceCards.forEach(card => {
          if (filter === 'all' || card.getAttribute('data-category') === filter) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // 4. Statistics Animated Counter
  const stats = document.querySelectorAll('.stat-number');
  let statsCounted = false;

  function countUp(el) {
    const target = parseInt(el.getAttribute('data-target'), 10);
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1800;
    const stepTime = 20;
    const steps = duration / stepTime;
    const increment = target / steps;
    let current = 0;

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        el.textContent = target.toLocaleString() + suffix;
        clearInterval(timer);
      } else {
        el.textContent = Math.floor(current).toLocaleString() + suffix;
      }
    }, stepTime);
  }

  if (stats.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !statsCounted) {
          statsCounted = true;
          stats.forEach(stat => countUp(stat));
        }
      });
    }, { threshold: 0.3 });

    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
      observer.observe(statsSection);
    }
  }

  // 5. Booking & Quotation Modal Handler with Real Backend
  const modalOverlay = document.getElementById('bookingModal');
  const openModalBtns = document.querySelectorAll('[data-open-modal="booking"]');
  const closeModalBtns = document.querySelectorAll('[data-close-modal]');
  const bookingForm = document.getElementById('quickBookingForm');
  const bookingSuccessBox = document.getElementById('bookingSuccessBox');
  const dateInput = document.getElementById('bookDate');
  const dateBadge = document.getElementById('dateAvailabilityBadge');

  function openModal(serviceName = '') {
    if (!modalOverlay) return;
    if (bookingSuccessBox) bookingSuccessBox.style.display = 'none';
    if (bookingForm) bookingForm.style.display = 'block';

    if (serviceName) {
      const selectService = document.getElementById('bookService');
      if (selectService) {
        let matched = false;
        for (let option of selectService.options) {
          if (option.value.toLowerCase().includes(serviceName.toLowerCase()) || 
              option.text.toLowerCase().includes(serviceName.toLowerCase())) {
            selectService.value = option.value;
            matched = true;
            break;
          }
        }
        if (!matched) {
          selectService.value = selectService.options[0].value;
        }
      }
    }
    modalOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    if (!modalOverlay) return;
    modalOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
  }

  openModalBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const service = btn.getAttribute('data-service') || '';
      openModal(service);
    });
  });

  closeModalBtns.forEach(btn => {
    btn.addEventListener('click', closeModal);
  });

  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeModal();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
      closeModal();
    }
  });

  // Date Availability Live Check (Phase 3)
  if (dateInput && dateBadge) {
    const bookSubmitBtn = bookingForm ? bookingForm.querySelector('button[type="submit"]') : null;

    dateInput.addEventListener('change', async () => {
      const dt = dateInput.value;
      if (!dt) {
        dateBadge.style.display = 'none';
        if (bookSubmitBtn) bookSubmitBtn.disabled = false;
        return;
      }
      dateBadge.style.display = 'inline-block';
      dateBadge.style.background = '#f1f5f9';
      dateBadge.style.color = '#475569';
      dateBadge.textContent = 'Checking crew availability...';

      try {
        const res = await fetch(`api/check-availability.php?date=${encodeURIComponent(dt)}`);
        const data = await res.json();
        if (data.success) {
          dateBadge.style.display = 'inline-block';
          dateBadge.textContent = data.status;
          dateBadge.style.color = data.color || '#166534';
          dateBadge.style.background = data.can_book ? (data.remaining_slots <= 2 ? '#fef3c7' : '#dcfce7') : '#fee2e2';

          if (bookSubmitBtn) {
            if (!data.can_book) {
              bookSubmitBtn.disabled = true;
              bookSubmitBtn.title = data.message || 'Date is fully booked';
            } else {
              bookSubmitBtn.disabled = false;
              bookSubmitBtn.title = '';
            }
          }
        }
      } catch (e) {
        dateBadge.style.display = 'none';
        if (bookSubmitBtn) bookSubmitBtn.disabled = false;
      }
    });
  }

  // Real Booking Form API Submission
  if (bookingForm) {
    bookingForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = bookingForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting to Database...';
      submitBtn.disabled = true;

      const payload = {
        name: document.getElementById('bookName').value.trim(),
        phone: document.getElementById('bookPhone').value.trim(),
        email: document.getElementById('bookEmail').value.trim(),
        event_date: document.getElementById('bookDate').value,
        event_type: document.getElementById('bookEventType').value,
        service_package: document.getElementById('bookService').value,
        location: document.getElementById('bookLocation').value.trim(),
        guest_count: document.getElementById('bookGuests').value,
        notes: document.getElementById('bookMessage').value.trim()
      };

      try {
        const response = await fetch('api/book.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        if (data.success) {
          bookingForm.style.display = 'none';
          if (bookingSuccessBox) {
            document.getElementById('successRefCode').textContent = data.reference_code;
            document.getElementById('btnTrackSuccess').href = `track.php?ref=${encodeURIComponent(data.reference_code)}`;
            bookingSuccessBox.style.display = 'block';
          } else {
            alert(`Booking successful! Your Reference Code is: ${data.reference_code}`);
            closeModal();
          }
          bookingForm.reset();
          if (dateBadge) dateBadge.style.display = 'none';
        } else {
          alert('Submission Error: ' + (data.message || 'Please verify form fields.'));
        }
      } catch (err) {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        alert('Network or server error while submitting booking: ' + err.message);
      }
    });
  }

  // 6. Review Modal & Star Rating Interaction
  const reviewModal = document.getElementById('reviewModal');
  const openReviewBtns = document.querySelectorAll('[data-open-modal="review"]');
  const reviewForm = document.getElementById('publicReviewForm');
  const reviewSuccessBox = document.getElementById('reviewSuccessBox');
  const reviewErrorAlert = document.getElementById('reviewErrorAlert');
  const starItems = document.querySelectorAll('.star-item');
  const revRatingInput = document.getElementById('revRating');
  const ratingText = document.getElementById('ratingText');

  const ratingDescriptions = {
    1: '1 - Poor',
    2: '2 - Needs Improvement',
    3: '3 - Good & Entertaining',
    4: '4 - Very Good Show',
    5: '5 - Outstanding & Magical Experience!'
  };

  function openReviewModal() {
    if (!reviewModal) return;
    if (reviewSuccessBox) reviewSuccessBox.style.display = 'none';
    if (reviewForm) {
      reviewForm.style.display = 'block';
      reviewForm.reset();
    }
    if (reviewErrorAlert) reviewErrorAlert.style.display = 'none';
    setStarRating(5);
    reviewModal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeReviewModal() {
    if (!reviewModal) return;
    reviewModal.classList.remove('active');
    document.body.style.overflow = 'auto';
  }

  function setStarRating(val) {
    if (revRatingInput) revRatingInput.value = val;
    if (ratingText) ratingText.textContent = ratingDescriptions[val] || `${val} Stars`;
    starItems.forEach(star => {
      const sVal = parseInt(star.getAttribute('data-value'), 10);
      if (sVal <= val) {
        star.style.color = '#f59e0b';
      } else {
        star.style.color = '#cbd5e1';
      }
    });
  }

  starItems.forEach(star => {
    star.addEventListener('click', () => {
      const val = parseInt(star.getAttribute('data-value'), 10);
      setStarRating(val);
    });
    star.addEventListener('mouseenter', () => {
      const val = parseInt(star.getAttribute('data-value'), 10);
      starItems.forEach(s => {
        const sVal = parseInt(s.getAttribute('data-value'), 10);
        s.style.color = (sVal <= val) ? '#f59e0b' : '#cbd5e1';
      });
    });
  });

  const starGroup = document.getElementById('starRatingGroup');
  if (starGroup) {
    starGroup.addEventListener('mouseleave', () => {
      const currentVal = parseInt(revRatingInput.value, 10) || 5;
      setStarRating(currentVal);
    });
  }

  openReviewBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openReviewModal();
    });
  });

  if (reviewModal) {
    reviewModal.addEventListener('click', (e) => {
      if (e.target === reviewModal) closeReviewModal();
    });
    const closeBtns = reviewModal.querySelectorAll('[data-close-modal]');
    closeBtns.forEach(b => b.addEventListener('click', closeReviewModal));
  }

  // Review Form Submit
  if (reviewForm) {
    reviewForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById('btnSubmitReview');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
      submitBtn.disabled = true;
      if (reviewErrorAlert) reviewErrorAlert.style.display = 'none';

      const payload = {
        client_name: document.getElementById('revName').value.trim(),
        location: document.getElementById('revLocation').value.trim(),
        event_type: document.getElementById('revEventType').value,
        reference_code: document.getElementById('revRefCode').value.trim(),
        rating: parseInt(document.getElementById('revRating').value, 10) || 5,
        comment: document.getElementById('revComment').value.trim()
      };

      try {
        const res = await fetch('api/submit-review.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        if (data.success) {
          reviewForm.style.display = 'none';
          if (reviewSuccessBox) reviewSuccessBox.style.display = 'block';
        } else {
          if (reviewErrorAlert) {
            reviewErrorAlert.textContent = data.message || 'Submission failed. Please check fields.';
            reviewErrorAlert.style.display = 'block';
          } else {
            alert('Review Error: ' + data.message);
          }
        }
      } catch (err) {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        if (reviewErrorAlert) {
          reviewErrorAlert.textContent = 'Network error: ' + err.message;
          reviewErrorAlert.style.display = 'block';
        } else {
          alert('Network error: ' + err.message);
        }
      }
    });
  }
});

