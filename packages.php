<?php
$pageTitle = "Party Packages & Pricing | Roppets Edutainment Production Inc.";
$currentNav = "packages";
include_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$packages = [];
try {
    $pdo = getDBConnection();
    if ($pdo) {
        $packages = $pdo->query("SELECT * FROM packages WHERE is_active = TRUE ORDER BY sort_order ASC, price ASC")->fetchAll();
    }
} catch (Exception $e) {
    error_log("Packages page DB offline notice: " . $e->getMessage());
}

if (empty($packages)) {
    $packages = [
        [
            'name' => 'Package A: Classic Puppet Fiesta',
            'subtitle' => 'Perfect for Kiddie Birthday Parties (1.5 Hours)',
            'price' => 15000,
            'is_popular' => false,
            'features' => "1.5 Hour Comprehensive Program\nHand Puppet Show (3 Moral Stories)\nProfessional Party Host & Game Master\nClassroom / Living Room Stage Setup\nSound System with Sound Effects"
        ],
        [
            'name' => 'Package B: Grand Edutainment Extravaganza',
            'subtitle' => 'Our Most Popular All-In-One Family Celebration',
            'price' => 25000,
            'is_popular' => true,
            'features' => "Full 2.5 Hour Event Production\nHand Puppet Show or Marionette Show\nThe Original Bubble Show (3 Performers)\nFancy Comedy Magic Show\n1 Character Mascot Appearance\nFull Sound System & Operator"
        ],
        [
            'name' => 'Package C: Ultimate Festival Spectacular',
            'subtitle' => 'Designed for School Fairs, Malls & Corporate Family Days',
            'price' => 38000,
            'is_popular' => false,
            'features' => "Full 3.5 Hour Stage Production\nTeatro Itim (Black Theater Show)\nThe Famous Bubble Factory Show\nMaster Marionette (Tali Galaw) Act\n2 Character Mascots & Party Hosts\nPro Audio & Stage Lighting Rig"
        ]
    ];
}
?>

  <!-- Page Header -->
  <div style="background: linear-gradient(135deg, #1b1622 0%, #2f1d3a 100%); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
      <span class="section-badge" style="background: rgba(255,255,255,0.15); color: #fff;">Transparent Rates</span>
      <h1 style="font-size: 40px; color: #fff; margin-top: 10px;">Party Packages & Pricelist</h1>
      <p style="color: #cbd5e1; max-width: 700px; margin: 10px auto 0 auto; font-size: 16px;">
        Curated edutainment packages offering unbeatable fun, moral education, and high-energy excitement for your special events.
      </p>
    </div>
  </div>

  <!-- Packages Grid -->
  <section class="section-padding" style="background: #ffffff;">
    <div class="container">
      <div class="packages-grid">
        <?php if (!empty($packages)): ?>
          <?php foreach ($packages as $p): 
            $isPopular = !empty($p['is_popular']);
            $pkgName = $p['name'] ?? 'Package';
            $pkgPrice = floatval($p['price'] ?? 0);
          ?>
            <div class="package-card <?php echo $isPopular ? 'popular' : ''; ?>">
              <?php if ($isPopular): ?>
                <span class="popular-badge">Best Seller</span>
              <?php endif; ?>
              <div class="package-header">
                <h3 class="package-name"><?php echo htmlspecialchars($pkgName); ?></h3>
                <?php if (!empty($p['subtitle'])): ?>
                  <p class="package-subtitle"><?php echo htmlspecialchars($p['subtitle']); ?></p>
                <?php endif; ?>
                <div class="package-price">₱<?php echo number_format($pkgPrice, 0); ?></div>
              </div>
              <?php if (!empty($p['features'])): ?>
                <ul class="package-features">
                  <?php 
                    $feats = explode("\n", $p['features']);
                    foreach ($feats as $f): 
                      $f = trim($f);
                      if (empty($f)) continue;
                  ?>
                    <li><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($f); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <button type="button" class="btn <?php echo $isPopular ? 'btn-primary' : 'btn-outline'; ?>" data-open-modal="booking" data-service="<?php echo htmlspecialchars($pkgName); ?>">Book Package</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; color: var(--muted-text); width: 100%;">No party packages currently available.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Interactive Package Calculator -->
  <section class="section-padding" style="background: #faf5f9;">
    <div class="container" style="max-width: 800px;">
      <div class="section-header">
        <span class="section-badge">Custom Estimator</span>
        <h2 class="section-title">Instant Price Estimator</h2>
        <p class="section-subtitle">Select your base package and add-on party activities to estimate your total event investment.</p>
      </div>

      <div style="background: #ffffff; border-radius: var(--border-radius-lg); padding: 35px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
        <div class="form-group">
          <label class="form-label" style="font-size: 15px;">1. Select Base Package</label>
          <select id="calcBasePackage" class="form-control" style="font-weight: 600;">
            <?php foreach ($packages as $idx => $p): 
              $isPop = !empty($p['is_popular']);
              $pName = $p['name'] ?? 'Package';
              $pPrice = floatval($p['price'] ?? 0);
            ?>
              <option value="<?php echo intval($pPrice); ?>" data-name="<?php echo htmlspecialchars($pName); ?>" <?php echo ($isPop || $idx === 0) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($pName); ?><?php echo $isPop ? ' (Best Value)' : ''; ?> — ₱<?php echo number_format($pPrice, 0); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" style="margin-top: 25px;">
          <label class="form-label" style="font-size: 15px;">2. Optional Party Add-Ons</label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" class="calc-addon" value="2500"> Face Painting (2 hrs) (+₱2,500)
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" class="calc-addon" value="2500"> Balloon Twisting (2 hrs) (+₱2,500)
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" class="calc-addon" value="3000"> Glitter Body Art / Henna (+₱3,000)
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" class="calc-addon" value="3500"> Extra Character Mascot (+₱3,500)
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" class="calc-addon" value="5000"> Photobooth (2 hrs Unlimited) (+₱5,000)
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" class="calc-addon" value="7000"> Photo & Video Coverage (+₱7,000)
            </label>
          </div>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #faf5f9; border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
          <div>
            <span style="font-size: 13px; color: var(--muted-text); text-transform: uppercase; font-weight: 700;">Estimated Package Total:</span>
            <div id="calcTotalDisplay" style="font-family: var(--font-heading); font-size: 34px; font-weight: 800; color: var(--primary);">₱0</div>
          </div>
          <button type="button" class="btn btn-primary btn-lg" id="btnBookCalculated" data-open-modal="booking">
            <i class="fa-solid fa-calendar-check"></i> Book This Selection
          </button>
        </div>
      </div>
    </div>
  </section>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const baseSelect = document.getElementById('calcBasePackage');
      const addOnCheckboxes = document.querySelectorAll('.calc-addon');
      const totalDisplay = document.getElementById('calcTotalDisplay');
      const bookBtn = document.getElementById('btnBookCalculated');

      function calculateTotal() {
        let base = parseInt(baseSelect.value, 10) || 0;
        let addonsTotal = 0;
        addOnCheckboxes.forEach(cb => {
          if (cb.checked) {
            addonsTotal += parseInt(cb.value, 10);
          }
        });
        const total = base + addonsTotal;
        totalDisplay.textContent = '₱' + total.toLocaleString();

        const selectedOption = baseSelect.options[baseSelect.selectedIndex];
        const pkgName = selectedOption.getAttribute('data-name');
        bookBtn.setAttribute('data-service', pkgName);
      }

      baseSelect.addEventListener('change', calculateTotal);
      addOnCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));
      calculateTotal();
    });
  </script>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
