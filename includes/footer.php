  <!-- Footer Section -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <!-- Col 1: About & Trust -->
        <div class="footer-about">
          <div class="brand-logo">
            <img src="assets/images/logo.png" alt="Roppets Edutainment Production Inc.">
          </div>
          <p>
            Roppets Edutainment Production Inc. has been pioneering the art of puppetry, bubble magic, and children's entertainment in the Philippines for over 36 years. Member of <em>Samahan ng mga Papetir ng Pilipinas</em> and <em>Puppeteers of America</em>.
          </p>
          <div class="top-bar-socials" style="justify-content: flex-start;">
            <a href="https://www.facebook.com/RoppetsEdutainmentProduction/" target="_blank" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://www.youtube.com/" target="_blank" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://www.instagram.com/" target="_blank" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="mailto:edutainment@roppets.com" title="Email"><i class="fa-solid fa-envelope"></i></a>
          </div>
        </div>

        <!-- Col 2: Entertainment Shows -->
        <div class="footer-col">
          <h3>Popular Shows</h3>
          <ul class="footer-links">
            <li><a href="services.php#puppet"><i class="fa-solid fa-angle-right"></i> Original Hand Puppet Show</a></li>
            <li><a href="services.php#bubble"><i class="fa-solid fa-angle-right"></i> The Bubble Factory Show</a></li>
            <li><a href="services.php#teatro-itim"><i class="fa-solid fa-angle-right"></i> Teatro Itim Black Theater</a></li>
            <li><a href="services.php#marionette"><i class="fa-solid fa-angle-right"></i> Tali Galaw Marionettes</a></li>
            <li><a href="services.php#mascots"><i class="fa-solid fa-angle-right"></i> Character Mascots</a></li>
            <li><a href="services.php#magic"><i class="fa-solid fa-angle-right"></i> Fancy Magic & Illusion</a></li>
          </ul>
        </div>

        <!-- Col 3: Fabrication & Events -->
        <div class="footer-col">
          <h3>Services & Fabrication</h3>
          <ul class="footer-links">
            <li><a href="fabrication.php#mascots"><i class="fa-solid fa-angle-right"></i> Corporate Mascot Making</a></li>
            <li><a href="fabrication.php#stuffed"><i class="fa-solid fa-angle-right"></i> Stuffed Toy Construction</a></li>
            <li><a href="fabrication.php#puppets"><i class="fa-solid fa-angle-right"></i> Custom Puppet Making</a></li>
            <li><a href="packages.php"><i class="fa-solid fa-angle-right"></i> Birthday Party Packages</a></li>
            <li><a href="services.php#activities"><i class="fa-solid fa-angle-right"></i> Face Painting & Nail Art</a></li>
            <li><a href="services.php#needs"><i class="fa-solid fa-angle-right"></i> Sound System & Lights</a></li>
          </ul>
        </div>

        <!-- Col 4: Contact & Office -->
        <div class="footer-col">
          <h3>Contact Office</h3>
          <ul class="footer-contact">
            <li>
              <i class="fa-solid fa-location-dot"></i>
              <span>Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Philippines</span>
            </li>
            <li>
              <i class="fa-solid fa-phone"></i>
              <span>(02) 8912-3456 / +63 917 530 0823</span>
            </li>
            <li>
              <i class="fa-solid fa-envelope"></i>
              <span>edutainment@roppets.com</span>
            </li>
            <li>
              <i class="fa-solid fa-clock"></i>
              <span>Mon – Sat: 8:00 AM – 5:00 PM</span>
            </li>
          </ul>
          <div class="payment-badges">
            <h4 style="font-size: 13px; color: #cbd5e1; margin-bottom: 8px; font-weight: 600;">Accepted Payment Channels</h4>
            <img src="assets/images/bank_payments.png" alt="Bank and Online Payments" onerror="this.src='assets/images/payments.png'">
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <strong>Roppets Edutainment Production Inc.</strong> All Rights Reserved. Designed & Developed for Client.</p>
      </div>
    </div>
  </footer>

  <!-- Include Reusable Booking Modal -->
  <?php include_once __DIR__ . '/booking-modal.php'; ?>

  <!-- Include Interactive Review Submission Modal -->
  <?php include_once __DIR__ . '/review-modal.php'; ?>

  <!-- AI Chatbot Floating Widget Component -->
  <?php include_once __DIR__ . '/chatbot.php'; ?>

  <!-- Main JavaScript File -->
  <script src="assets/js/main.js"></script>
</body>
</html>
