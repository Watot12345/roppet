<?php
$pageTitle = "Track Your Booking Status | Roppets Edutainment Production Inc.";
$currentNav = "track";
include_once __DIR__ . '/includes/header.php';
?>

  <!-- Page Header -->
  <div style="background: linear-gradient(135deg, #1b1622 0%, #2f1d3a 100%); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
      <span class="section-badge" style="background: rgba(255,255,255,0.15); color: #fff;">Client Self-Service Portal</span>
      <h1 style="font-size: 40px; color: #fff; margin-top: 10px;">Track Event Reservation & Payments</h1>
      <p style="color: #cbd5e1; max-width: 700px; margin: 10px auto 0 auto; font-size: 16px;">
        Enter your Roppets Reference Code (e.g., <code>ROP-2026-9414</code>) to check live reservation status, view your official quotation, or pay downpayment online.
      </p>
    </div>
  </div>

  <section class="section-padding" style="background: #ffffff; min-height: 550px;">
    <div class="container" style="max-width: 800px;">
      
      <!-- Lookup Form Card -->
      <div style="background: #ffffff; border-radius: var(--border-radius-lg); padding: 35px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color); margin-bottom: 35px;">
        <form id="trackForm">
          <label class="form-label" style="font-size: 16px; margin-bottom: 8px;">Booking Reference Code</label>
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" id="trackInput" class="form-control" style="flex: 1; font-family: monospace; font-size: 16px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;" placeholder="ROP-2026-XXXX" required>
            <button type="submit" class="btn btn-primary btn-lg" id="trackBtn">
              <i class="fa-solid fa-magnifying-glass"></i> Check Status
            </button>
          </div>
        </form>
      </div>

      <!-- Result Card (Hidden by default) -->
      <div id="trackResult" style="display: none;">
        
        <div style="background: #faf5f9; border-radius: var(--border-radius-lg); border: 1px solid rgba(145,58,124,0.2); padding: 35px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(145,58,124,0.15); padding-bottom: 18px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <div>
              <span style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: var(--muted-text);">Reference Code</span>
              <h3 id="resRefCode" style="font-family: monospace; font-size: 26px; color: var(--primary); margin-top: 2px;">ROP-2026-XXXX</h3>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
              <span id="resStatusBadge" style="display: inline-block; padding: 6px 18px; border-radius: var(--border-radius-full); font-weight: 700; font-size: 14px; background: #e2e8f0; color: #334155;">Pending</span>
              <a href="#" id="btnViewQuote" target="_blank" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-file-invoice"></i> View / Print Quotation
              </a>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
              <span style="font-size: 12px; color: var(--muted-text); text-transform: uppercase; font-weight: 600;">Client Name:</span>
              <div id="resName" style="font-weight: 600; color: var(--dark-text); font-size: 15px;">-</div>
            </div>
            <div>
              <span style="font-size: 12px; color: var(--muted-text); text-transform: uppercase; font-weight: 600;">Event Date:</span>
              <div id="resEventDate" style="font-weight: 600; color: var(--dark-text); font-size: 15px;">-</div>
            </div>
            <div>
              <span style="font-size: 12px; color: var(--muted-text); text-transform: uppercase; font-weight: 600;">Booked Service / Package:</span>
              <div id="resService" style="font-weight: 600; color: var(--primary); font-size: 15px;">-</div>
            </div>
            <div>
              <span style="font-size: 12px; color: var(--muted-text); text-transform: uppercase; font-weight: 600;">Venue Location:</span>
              <div id="resLocation" style="font-weight: 600; color: var(--dark-text); font-size: 15px;">-</div>
            </div>
            <div>
              <span style="font-size: 12px; color: var(--muted-text); text-transform: uppercase; font-weight: 600;">Estimated Total Amount:</span>
              <div id="resPrice" style="font-weight: 700; color: var(--dark-text); font-size: 16px;">-</div>
            </div>
            <div>
              <span style="font-size: 12px; color: var(--muted-text); text-transform: uppercase; font-weight: 600;">Booked On:</span>
              <div id="resCreatedAt" style="font-size: 14px; color: var(--muted-text);">-</div>
            </div>
          </div>

          <!-- Status Instructions -->
          <div id="resNextSteps" style="background: #ffffff; padding: 20px; border-radius: var(--border-radius-md); border-left: 4px solid var(--primary); font-size: 14px; color: #475569;">
            <strong>Next Step:</strong> Lock in your cast and production schedule by paying the 50% downpayment below.
          </div>
        </div>

        <!-- PAYMENT CHANNELS: Option A (Instant PayMongo Checkout) & Option B (Manual Upload) -->
        <div style="background: #ffffff; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 35px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
          <span class="section-badge">Payment Gateway</span>
          <h3 style="font-size: 22px; margin: 8px 0 20px 0;">Secure Event Downpayment</h3>

          <!-- OPTION A: INSTANT PAYMONGO CHECKOUT (GCASH, MAYA, CARD) -->
          <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: var(--border-radius-md); padding: 25px; margin-bottom: 30px; text-align: center;">
            <span class="badge" style="background: #16a34a; color: #fff; font-size: 11px; margin-bottom: 6px;">RECOMMENDED &bull; INSTANT AUTO-CONFIRM</span>
            <h4 style="font-size: 20px; color: #166534; margin: 6px 0 8px 0;">Pay with GCash, Maya, GrabPay or Credit/Debit Card</h4>
            <p style="color: #14532d; font-size: 13.5px; margin-bottom: 18px;">
              Pay securely via PayMongo Checkout. Your booking status will be automatically verified in seconds!
            </p>

            <button type="button" id="btnInstantCheckout" class="btn btn-primary btn-lg" style="background: #16a34a; border-color: #16a34a; font-size: 16px; padding: 14px 30px; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);">
              <i class="fa-solid fa-bolt"></i> Pay 50% Downpayment Online (₱<span id="instantDownpaymentDisplay">0</span>)
            </button>
            <div style="margin-top: 10px; font-size: 12px; color: #166534;">
              <i class="fa-solid fa-lock"></i> 256-Bit SSL Encrypted &bull; PayMongo Certified
            </div>
          </div>

          <div style="text-align: center; margin: 25px 0; position: relative;">
            <hr style="border: 0; border-top: 1px solid var(--border-color);">
            <span style="position: absolute; top: -11px; left: 50%; transform: translateX(-50%); background: #fff; padding: 0 15px; font-size: 12px; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Or Pay via Manual Bank Deposit</span>
          </div>

          <!-- OPTION B: MANUAL BANK DETAILS & PROOF UPLOAD -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
            <div style="background: #faf5f9; padding: 15px; border-radius: 8px; border: 1px solid rgba(133,46,112,0.15);">
              <strong style="color: var(--primary); font-size: 14px;">BDO Current Account</strong>
              <div style="font-size: 13px; margin-top: 4px;">
                Account: Roppets Edutainment Production Inc.<br>
                Number: <code>0012-3456-7890</code>
              </div>
            </div>
            <div style="background: #faf5f9; padding: 15px; border-radius: 8px; border: 1px solid rgba(133,46,112,0.15);">
              <strong style="color: var(--primary); font-size: 14px;">GCash / Maya (Finance)</strong>
              <div style="font-size: 13px; margin-top: 4px;">
                Account Name: Roppets Production<br>
                Mobile: <code>0917-530-0823</code>
              </div>
            </div>
          </div>

          <form id="paymentUploadForm" enctype="multipart/form-data">
            <input type="hidden" id="payRefCode" name="reference_code" value="">

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Payment Channel *</label>
                <select name="payment_method" class="form-control" required>
                  <option value="GCash">GCash</option>
                  <option value="Maya">Maya</option>
                  <option value="BDO Bank Transfer">BDO Bank Transfer</option>
                  <option value="BPI Bank Transfer">BPI Bank Transfer</option>
                  <option value="Other">Other Bank Transfer</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Amount Paid (₱) *</label>
                <input type="number" name="amount" id="payAmountInput" class="form-control" placeholder="e.g. 5750" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Transaction / Reference No. *</label>
                <input type="text" name="transaction_ref" class="form-control" placeholder="e.g. GCash Ref 100293848" required>
              </div>

              <div class="form-group">
                <label class="form-label">Attach Receipt Screenshot / PDF *</label>
                <input type="file" name="receipt_file" class="form-control" accept="image/*,.pdf" required>
              </div>
            </div>

            <button type="submit" id="btnUploadPay" class="btn btn-outline btn-lg" style="width: 100%;">
              <i class="fa-solid fa-cloud-arrow-up"></i> Upload Manual Bank Slip / Receipt
            </button>
          </form>

          <div id="paySuccessAlert" style="display: none; background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center;">
            <i class="fa-solid fa-circle-check" style="font-size: 20px; margin-bottom: 4px;"></i>
            <div style="font-weight: 600;">Receipt Uploaded Successfully!</div>
            <div style="font-size: 13px;">Our finance staff is verifying your payment and will confirm your schedule within 1 business day.</div>
          </div>
        </div>

        <!-- Leave a Review Section -->
        <div style="background: #faf5f9; border-radius: var(--border-radius-lg); border: 1px solid rgba(145, 58, 124, 0.15); padding: 35px;">
          <span class="section-badge">Client Feedback</span>
          <h3 style="font-size: 22px; margin: 8px 0 16px 0;">Rate & Review Your Roppets Experience</h3>
          
          <form id="reviewSubmitForm">
            <input type="hidden" id="revClientName" name="client_name" value="">
            <input type="hidden" id="revLocation" name="location" value="">

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Your Star Rating *</label>
                <select name="rating" class="form-control" style="font-weight: 700; color: #f59e0b;">
                  <option value="5">⭐⭐⭐⭐⭐ 5 Stars (Outstanding Experience)</option>
                  <option value="4">⭐⭐⭐⭐ 4 Stars (Very Good)</option>
                  <option value="3">⭐⭐⭐ 3 Stars (Good)</option>
                  <option value="2">⭐⭐ 2 Stars (Needs Improvement)</option>
                  <option value="1">⭐ 1 Star (Poor)</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">City / Location</label>
                <input type="text" id="revCityInput" class="form-control" placeholder="e.g. Quezon City">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Your Review / Testimonial *</label>
              <textarea name="comment" class="form-control" rows="3" placeholder="Tell us how the kids enjoyed the puppet show, bubble factory, or mascot..." required></textarea>
            </div>

            <button type="submit" id="btnSubmitReview" class="btn btn-secondary btn-lg" style="width: 100%;">
              <i class="fa-solid fa-star"></i> Submit Review & Rating
            </button>
          </form>

          <div id="reviewSuccessAlert" style="display: none; background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 15px; border-radius: 8px; margin-top: 15px; text-align: center;">
            <i class="fa-solid fa-circle-check"></i> Thank you! Your review has been submitted and added to our feedback wall.
          </div>
        </div>

      </div>

      <!-- Not Found Alert -->
      <div id="trackNotFound" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 20px; border-radius: var(--border-radius-md); text-align: center;">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 24px; margin-bottom: 8px;"></i>
        <p style="font-weight: 600;">No booking reservation found for that reference code.</p>
        <p style="font-size: 13.5px; margin-top: 4px;">Please double-check your reference code or contact our office at (02) 8912-3456.</p>
      </div>

    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const trackForm = document.getElementById('trackForm');
      const trackInput = document.getElementById('trackInput');
      const trackBtn = document.getElementById('trackBtn');
      const trackResult = document.getElementById('trackResult');
      const trackNotFound = document.getElementById('trackNotFound');
      const payForm = document.getElementById('paymentUploadForm');
      const payRefCode = document.getElementById('payRefCode');
      const btnViewQuote = document.getElementById('btnViewQuote');
      const paySuccessAlert = document.getElementById('paySuccessAlert');
      const reviewForm = document.getElementById('reviewSubmitForm');
      const reviewSuccessAlert = document.getElementById('reviewSuccessAlert');
      const btnInstantCheckout = document.getElementById('btnInstantCheckout');
      const instantDownpaymentDisplay = document.getElementById('instantDownpaymentDisplay');

      let currentBooking = null;

      // Auto fill from URL param ?ref=...
      const urlParams = new URLSearchParams(window.location.search);
      const initialRef = urlParams.get('ref');
      if (initialRef) {
        trackInput.value = initialRef;
        doTrack(initialRef);
      }

      trackForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const ref = trackInput.value.trim();
        if (ref) doTrack(ref);
      });

      function doTrack(ref) {
        trackBtn.disabled = true;
        trackBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';
        trackResult.style.display = 'none';
        trackNotFound.style.display = 'none';
        paySuccessAlert.style.display = 'none';
        reviewSuccessAlert.style.display = 'none';

        fetch(`api/track.php?ref=${encodeURIComponent(ref)}`)
          .then(res => res.json())
          .then(data => {
            trackBtn.disabled = false;
            trackBtn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Check Status';

            if (data.success && data.booking) {
              const b = data.booking;
              currentBooking = b;
              document.getElementById('resRefCode').textContent = b.reference_code;
              payRefCode.value = b.reference_code;
              btnViewQuote.href = `invoice.php?ref=${encodeURIComponent(b.reference_code)}`;

              document.getElementById('resName').textContent = b.name;
              document.getElementById('revClientName').value = b.name;
              document.getElementById('revCityInput').value = b.location || '';

              document.getElementById('resEventDate').textContent = b.event_date;
              document.getElementById('resService').textContent = b.service_package;
              document.getElementById('resLocation').textContent = b.location || 'To be confirmed';
              
              const price = parseFloat(b.total_price) || 0;
              document.getElementById('resPrice').textContent = price > 0 ? '₱' + price.toLocaleString() : 'Quotation in progress';
              
              const downpayment = price > 0 ? (price * 0.5) : 3000;
              instantDownpaymentDisplay.textContent = downpayment.toLocaleString();
              document.getElementById('payAmountInput').value = downpayment;

              document.getElementById('resCreatedAt').textContent = new Date(b.created_at).toLocaleDateString();

              const badge = document.getElementById('resStatusBadge');
              badge.textContent = b.status;
              if (b.status === 'Confirmed' || b.status === 'Completed') {
                badge.style.background = '#dcfce7';
                badge.style.color = '#166534';
              } else if (b.status === 'Downpayment Paid') {
                badge.style.background = '#e0f2fe';
                badge.style.color = '#075985';
              } else if (b.status === 'Cancelled') {
                badge.style.background = '#fee2e2';
                badge.style.color = '#991b1b';
              } else {
                badge.style.background = '#fef3c7';
                badge.style.color = '#92400e';
              }

              trackResult.style.display = 'block';
            } else {
              trackNotFound.style.display = 'block';
            }
          })
          .catch(err => {
            trackBtn.disabled = false;
            trackBtn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Check Status';
            trackNotFound.style.display = 'block';
          });
      }

      // Instant PayMongo Online Checkout Trigger
      if (btnInstantCheckout) {
        btnInstantCheckout.addEventListener('click', async () => {
          if (!currentBooking) return;
          const orig = btnInstantCheckout.innerHTML;
          btnInstantCheckout.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Initializing PayMongo GCash / Card Checkout...';
          btnInstantCheckout.disabled = true;

          try {
            const res = await fetch('api/create-checkout.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ reference_code: currentBooking.reference_code })
            });
            const data = await res.json();

            if (data.success && data.checkout_url) {
              window.location.href = data.checkout_url;
            } else {
              btnInstantCheckout.innerHTML = orig;
              btnInstantCheckout.disabled = false;
              alert('Could not initialize gateway: ' + (data.message || 'Please try again.'));
            }
          } catch (e) {
            btnInstantCheckout.innerHTML = orig;
            btnInstantCheckout.disabled = false;
            alert('Gateway network error: ' + e.message);
          }
        });
      }

      // Payment manual upload handler
      if (payForm) {
        payForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const btn = document.getElementById('btnUploadPay');
          const orig = btn.innerHTML;
          btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading Receipt...';
          btn.disabled = true;

          const formData = new FormData(payForm);

          try {
            const res = await fetch('api/upload-payment.php', {
              method: 'POST',
              body: formData
            });
            const data = await res.json();
            btn.innerHTML = orig;
            btn.disabled = false;

            if (data.success) {
              paySuccessAlert.style.display = 'block';
              payForm.reset();
              payRefCode.value = trackInput.value.trim();
            } else {
              alert('Upload failed: ' + (data.message || 'Please check file format.'));
            }
          } catch (err) {
            btn.innerHTML = orig;
            btn.disabled = false;
            alert('Upload error: ' + err.message);
          }
        });
      }

      // Review submission handler
      if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const btn = document.getElementById('btnSubmitReview');
          const orig = btn.innerHTML;
          btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting Review...';
          btn.disabled = true;

          const payload = {
            client_name: document.getElementById('revClientName').value || document.getElementById('resName').textContent,
            location: document.getElementById('revCityInput').value,
            rating: reviewForm.querySelector('[name="rating"]').value,
            comment: reviewForm.querySelector('[name="comment"]').value
          };

          try {
            const res = await fetch('api/submit-review.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.innerHTML = orig;
            btn.disabled = false;

            if (data.success) {
              reviewSuccessAlert.style.display = 'block';
              reviewForm.reset();
            } else {
              alert('Review submission error: ' + data.message);
            }
          } catch (err) {
            btn.innerHTML = orig;
            btn.disabled = false;
            alert('Network error: ' + err.message);
          }
        });
      }
    });
  </script>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
