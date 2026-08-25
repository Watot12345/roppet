<!-- Quick Booking & Quotation Modal -->
<div class="modal-overlay" id="bookingModal">
  <div class="modal-card">
    <div class="modal-header">
      <button type="button" class="modal-close" data-close-modal title="Close">&times;</button>
      <h3><i class="fa-solid fa-calendar-star"></i> Book a Show / Request Quotation</h3>
      <p>Fill out the details below and our team will get back to you with a custom quote.</p>
    </div>

    <div class="modal-body">
      <!-- Submission Form -->
      <form id="quickBookingForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bookName">Full Name *</label>
            <input type="text" id="bookName" class="form-control" placeholder="e.g. Maria Santos" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="bookPhone">Contact Number *</label>
            <input type="tel" id="bookPhone" class="form-control" placeholder="e.g. 0917 123 4567" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bookEmail">Email Address *</label>
            <input type="email" id="bookEmail" class="form-control" placeholder="name@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="bookDate">
              Event Date * 
              <span id="dateAvailabilityBadge" style="display: none; font-size: 11px; padding: 2px 8px; border-radius: 12px; margin-left: 6px; font-weight: 600;"></span>
            </label>
            <input type="date" id="bookDate" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bookEventType">Event Type</label>
            <select id="bookEventType" class="form-control">
              <option value="Birthday Party">Kiddie Birthday Party</option>
              <option value="School Tour">School Tour / Educational Event</option>
              <option value="Mall Show">Mall Show / Public Appearance</option>
              <option value="Corporate Event">Corporate Event / Family Day</option>
              <option value="Custom Mascot">Custom Mascot / Plush Fabrication</option>
              <option value="Other">Other Special Occasion</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="bookService">Interested Service / Package</label>
            <select id="bookService" class="form-control">
              <option value="Roppets Hand Puppet Show">Roppets Hand Puppet Show (3 Plays)</option>
              <option value="The Bubble Factory Show">Bubble Show by The Bubble Factory</option>
              <option value="Teatro Itim Black Theater">Teatro Itim Black Theater Play</option>
              <option value="Cultural Marionette Show">Cultural Marionette Show (Tali Galaw)</option>
              <option value="Character Mascots">Character Mascots & Meet-and-Greet</option>
              <option value="Fancy Magic Show">Fancy Magic & Illusion Show</option>
              <option value="Kiddie Fun Package">Kiddie Fun Package (Basic)</option>
              <option value="Classic Combo Package">Classic Combo Package</option>
              <option value="Supreme Edutainment Package">Supreme Edutainment Package (Best Value)</option>
              <option value="Grand Extravaganza">Grand Extravaganza (Full Suite)</option>
              <option value="Custom Mascot Fabrication">Custom Mascot Fabrication</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bookLocation">Event Venue & City</label>
            <input type="text" id="bookLocation" class="form-control" placeholder="e.g. Quezon City / Makati / Alabang">
          </div>
          <div class="form-group">
            <label class="form-label" for="bookGuests">Approx. Number of Kids/Guests</label>
            <input type="number" id="bookGuests" class="form-control" placeholder="e.g. 50">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="bookMessage">Special Requests / Preferred Puppet Stories / Notes</label>
          <textarea id="bookMessage" class="form-control" rows="3" placeholder="Tell us if you have a specific party theme, favorite stories (3 Little Pigs, Red Riding Hood, etc.), or specific mascots needed..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
          <i class="fa-solid fa-paper-plane"></i> Submit Booking Inquiry
        </button>
      </form>

      <!-- Instant Success Feedback Box -->
      <div id="bookingSuccessBox" style="display: none; text-align: center; padding: 20px 10px;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 15px auto;">
          <i class="fa-solid fa-check"></i>
        </div>
        <h3 style="font-size: 24px; color: var(--dark-text); margin-bottom: 8px;">Reservation Received!</h3>
        <p style="color: var(--muted-text); font-size: 14.5px; margin-bottom: 20px;">
          Thank you! Your event booking details have been saved into our production schedule.
        </p>

        <div style="background: #faf5f9; border: 1px dashed var(--primary); border-radius: var(--border-radius-md); padding: 18px; margin-bottom: 25px;">
          <span style="font-size: 12px; text-transform: uppercase; color: var(--muted-text); font-weight: 700;">Your Tracking Reference Code</span>
          <div id="successRefCode" style="font-family: monospace; font-size: 26px; font-weight: 800; color: var(--primary); margin-top: 4px; letter-spacing: 1px;">ROP-2026-XXXX</div>
          <small style="color: var(--muted-text); font-size: 12px;">Save this reference code to track your booking status anytime.</small>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
          <a href="track.php" id="btnTrackSuccess" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass-location"></i> Track This Booking
          </a>
          <button type="button" class="btn btn-outline" data-close-modal>
            Close
          </button>
        </div>
      </div>

    </div>
  </div>
</div>
