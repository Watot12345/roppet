<?php
$pageTitle = "Contact Us & Bookings | Roppets Edutainment Production Inc.";
$currentNav = "contact";
include_once __DIR__ . '/includes/header.php';
?>

  <!-- Page Header -->
  <div style="background: linear-gradient(135deg, #1b1622 0%, #2f1d3a 100%); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
      <span class="section-badge" style="background: rgba(255,255,255,0.15); color: #fff;">Get In Touch</span>
      <h1 style="font-size: 40px; color: #fff; margin-top: 10px;">Contact Roppets Edutainment</h1>
      <p style="color: #cbd5e1; max-width: 700px; margin: 10px auto 0 auto; font-size: 16px;">
        Have a question about our party packages, mascot fabrication, or school tours? We'd love to hear from you.
      </p>
    </div>
  </div>

  <section class="section-padding" style="background: #ffffff;">
    <div class="container">
      <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 50px; align-items: start;">
        
        <!-- Contact Info Box -->
        <div>
          <span class="section-badge">Head Office</span>
          <h2 style="font-size: 28px; margin: 10px 0 20px 0;">Visit Our Production Studio</h2>
          <p style="color: var(--muted-text); margin-bottom: 25px;">
            Feel free to visit our office to check out puppet samples, discuss custom mascot fabrications, or plan large-scale school tours.
          </p>

          <div style="margin-bottom: 25px;">
            <div style="display: flex; gap: 15px; margin-bottom: 18px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(145,58,124,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                <i class="fa-solid fa-location-dot"></i>
              </div>
              <div>
                <h4 style="font-size: 16px; margin-bottom: 2px;">Main Office Address</h4>
                <p style="font-size: 14px; color: var(--muted-text);">Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Metro Manila, Philippines</p>
              </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 18px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(243,156,18,0.1); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                <i class="fa-solid fa-phone"></i>
              </div>
              <div>
                <h4 style="font-size: 16px; margin-bottom: 2px;">Phone Numbers</h4>
                <p style="font-size: 14px; color: var(--muted-text);">Landline: (02) 8912-3456<br>Mobile: +63 917 530 0823 / +63 922 850 1234</p>
              </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 18px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16,185,129,0.1); color: var(--accent-green); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                <i class="fa-solid fa-envelope"></i>
              </div>
              <div>
                <h4 style="font-size: 16px; margin-bottom: 2px;">Email Inquiries</h4>
                <p style="font-size: 14px; color: var(--muted-text);">edutainment@roppets.com<br>bookings@roppets.com</p>
              </div>
            </div>

            <div style="display: flex; gap: 15px;">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14,165,233,0.1); color: var(--accent-cyan); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                <i class="fa-solid fa-clock"></i>
              </div>
              <div>
                <h4 style="font-size: 16px; margin-bottom: 2px;">Operating Hours</h4>
                <p style="font-size: 14px; color: var(--muted-text);">Monday to Saturday: 8:00 AM – 5:00 PM<br>Sunday: Available by appointment for events</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact & Booking Form -->
        <div style="background: #ffffff; padding: 40px; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
          <h3 style="font-size: 22px; margin-bottom: 8px;">Send Us a Message</h3>
          <p style="color: var(--muted-text); font-size: 14px; margin-bottom: 25px;">Please fill in the form below and we will respond within 24 hours.</p>

          <form id="contactPageForm">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="contactName">Full Name *</label>
                <input type="text" id="contactName" class="form-control" placeholder="Your Name" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="contactPhone">Contact Number</label>
                <input type="tel" id="contactPhone" class="form-control" placeholder="e.g. 0917 123 4567">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="contactEmail">Email Address *</label>
                <input type="email" id="contactEmail" class="form-control" placeholder="name@example.com" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="contactService">Service of Interest</label>
                <select id="contactService" class="form-control">
                  <option>Hand Puppet Show</option>
                  <option>The Bubble Factory Show</option>
                  <option>Teatro Itim Black Theater</option>
                  <option>Party Packages</option>
                  <option>Custom Mascot / Plush Fabrication</option>
                  <option>School Tour / Field Trip</option>
                  <option>Other Inquiries</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="contactSubject">Subject</label>
              <input type="text" id="contactSubject" class="form-control" placeholder="Inquiry about..." required>
            </div>

            <div class="form-group">
              <label class="form-label" for="contactMessage">Message Details *</label>
              <textarea id="contactMessage" class="form-control" rows="4" placeholder="Tell us more about your event date, location, or questions..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmitContact" style="width: 100%;">
              <i class="fa-solid fa-paper-plane"></i> Send Message
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const contactForm = document.getElementById('contactPageForm');
      const submitBtn = document.getElementById('btnSubmitContact');

      if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const origText = submitBtn.innerHTML;
          submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending message...';
          submitBtn.disabled = true;

          const payload = {
            name: document.getElementById('contactName').value.trim(),
            email: document.getElementById('contactEmail').value.trim(),
            phone: document.getElementById('contactPhone').value.trim(),
            service: document.getElementById('contactService').value,
            subject: document.getElementById('contactSubject').value.trim(),
            message: document.getElementById('contactMessage').value.trim()
          };

          try {
            const res = await fetch('api/contact.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
            });
            const data = await res.json();
            submitBtn.innerHTML = origText;
            submitBtn.disabled = false;

            if (data.success) {
              alert(data.message || 'Thank you! Your message has been sent successfully.');
              contactForm.reset();
            } else {
              alert('Error: ' + (data.message || 'Unable to submit inquiry.'));
            }
          } catch (err) {
            submitBtn.innerHTML = origText;
            submitBtn.disabled = false;
            alert('Network error: ' + err.message);
          }
        });
      }
    });
  </script>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
