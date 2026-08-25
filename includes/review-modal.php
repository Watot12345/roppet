<!-- Interactive Customer Review & Rating Modal -->
<div class="modal-overlay" id="reviewModal">
  <div class="modal-card" style="max-width: 520px;">
    <div class="modal-header">
      <button type="button" class="modal-close" data-close-modal title="Close">&times;</button>
      <h3><i class="fa-solid fa-star" style="color: #f59e0b; margin-right: 6px;"></i> Share Your Roppets Experience</h3>
      <p>We value your feedback! Tell us about your puppet show, bubble party, or mascot experience.</p>
    </div>

    <div class="modal-body" style="padding: 24px;">
      <!-- Success Message Box -->
      <div id="reviewSuccessBox" style="display: none; text-align: center; padding: 25px 15px;">
        <div style="width: 65px; height: 65px; background: #dcfce7; color: #166534; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 16px auto;">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <h4 style="font-size: 20px; color: #1e293b; margin-bottom: 8px;">Review Submitted!</h4>
        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 20px;">
          Thank you for sharing your experience. Your feedback helps families and schools discover the magic of Roppets edutainment.
        </p>
        <button type="button" class="btn btn-primary" data-close-modal>Close</button>
      </div>

      <!-- Review Form -->
      <form id="publicReviewForm">
        <!-- Interactive Star Selector -->
        <div style="text-align: center; margin-bottom: 20px;">
          <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Your Overall Rating</label>
          <div id="starRatingGroup" style="display: inline-flex; gap: 8px; font-size: 28px; cursor: pointer; color: #cbd5e1;">
            <i class="fa-solid fa-star star-item" data-value="1" style="color: #f59e0b;"></i>
            <i class="fa-solid fa-star star-item" data-value="2" style="color: #f59e0b;"></i>
            <i class="fa-solid fa-star star-item" data-value="3" style="color: #f59e0b;"></i>
            <i class="fa-solid fa-star star-item" data-value="4" style="color: #f59e0b;"></i>
            <i class="fa-solid fa-star star-item" data-value="5" style="color: #f59e0b;"></i>
          </div>
          <input type="hidden" id="revRating" name="rating" value="5">
          <div id="ratingText" style="font-size: 12.5px; font-weight: 600; color: #f59e0b; margin-top: 4px;">5 - Excellent Experience!</div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="revName">Your Name *</label>
            <input type="text" id="revName" class="form-control" placeholder="e.g. Christine Mendoza" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="revLocation">City / Location</label>
            <input type="text" id="revLocation" class="form-control" placeholder="e.g. Quezon City, Metro Manila">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="revEventType">Show / Event Type</label>
            <select id="revEventType" class="form-control">
              <option value="Kiddie Birthday Party">Kiddie Birthday Party</option>
              <option value="Hand Puppet Show">Original Hand Puppet Show</option>
              <option value="The Bubble Factory Show">The Bubble Factory Show</option>
              <option value="Teatro Itim Black Theater">Teatro Itim Black Theater</option>
              <option value="School Tour / Family Day">School Tour / Family Day</option>
              <option value="Corporate / Mall Event">Corporate / Mall Event</option>
              <option value="Custom Mascot Order">Custom Mascot Fabrication</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="revRefCode">Reference Code <small style="color: #94a3b8;">(Optional)</small></label>
            <input type="text" id="revRefCode" class="form-control" placeholder="e.g. ROP-2026-9414">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="revComment">Your Feedback & Testimonial *</label>
          <textarea id="revComment" class="form-control" rows="4" placeholder="Tell us how the kids and guests enjoyed the puppetry, bubbles, magic, or mascots..." required></textarea>
        </div>

        <div id="reviewErrorAlert" style="display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 15px;"></div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
          <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" id="btnSubmitReview" class="btn btn-primary">
            <i class="fa-solid fa-paper-plane"></i> Submit Testimonial
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
