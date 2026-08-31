<?php
$pageTitle = "Roppets Edutainment Production Inc. | Puppet Shows, Bubble Shows, and Children's Entertainment";
$currentNav = "home";
include_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$activeServices = [];
$activePackages = [];
$approvedReviews = [];

try {
    $pdo = getDBConnection();
    if ($pdo) {
        $activeServices = $pdo->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY sort_order ASC, id ASC")->fetchAll();
        $activePackages = $pdo->query("SELECT * FROM packages WHERE is_active = TRUE ORDER BY sort_order ASC, price ASC")->fetchAll();
        $approvedReviews = $pdo->query("SELECT * FROM reviews WHERE is_approved = TRUE ORDER BY created_at DESC LIMIT 6")->fetchAll();
    }
} catch (Exception $e) {
    error_log("Index page DB offline notice: " . $e->getMessage());
}

// Resilient default fallback data if database is offline or tables are unseeded
if (empty($activeServices)) {
    $activeServices = [
        [
            'title' => 'Hand Puppet Shows',
            'category' => 'puppetry',
            'badge' => 'Classic Best-Seller',
            'image_url' => 'assets/images/smurfs-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png',
            'description' => 'Over 100+ moral-rich and educational stories. Features 3 exciting puppet plays with our signature colorful portable theater stage, vibrant characters, and voice acting.',
            'starting_price' => 8500,
            'features' => "3 Exciting Story Acts\nLive Voice Acting & Sound Effects\nPortable Theater Stage Setup\nMoral & Values-Based Lessons"
        ],
        [
            'title' => 'The Original Bubble Show',
            'category' => 'specialty',
            'badge' => 'High-Energy Spectacle',
            'image_url' => 'assets/images/hero_bubble.jpg',
            'description' => 'The famous Bubble Factory performance by 3 all-girl dancers. Giant smoke bubbles, rainbow bubble sculptures, and the exciting kid-inside-a-bubble finale.',
            'starting_price' => 9500,
            'features' => "3 Themed Girl Performers\nGiant Bubble & Smoke Tricks\nKid-in-a-Bubble Photo Op\nDynamic Dance Choreography"
        ],
        [
            'title' => 'Teatro Itim (Black Theater)',
            'category' => 'puppetry',
            'badge' => 'Ultraviolet Attraction',
            'image_url' => 'assets/images/black_theater.jpg',
            'description' => 'A mesmerizing ultraviolet glow-in-the-dark puppetry style that delights both kids and adults. Full-stage fluorescent visual effects and storytelling innovation.',
            'starting_price' => 12000,
            'features' => "Full Black Theater Stage Tent\nFluorescent Glowing Props\nMagical Levitating Puppetry\nCustom UV Lighting Rig"
        ],
        [
            'title' => 'Marionette Show (Tali Galaw)',
            'category' => 'puppetry',
            'badge' => 'Masterful Puppetry',
            'image_url' => 'assets/images/Tali-Galaw-Marionette-Group-1-265x265_c.png',
            'description' => 'Classical string-controlled marionette theater showcasing intricate puppet dances, comedic skits, and world-class Filipino folk performances.',
            'starting_price' => 8000,
            'features' => "Master String Puppeteers\nDancing Musical Skits\nInteractive Audience Play\nPhilippine Folk & Modern Acts"
        ],
        [
            'title' => 'Fancy Magic & Illusion Show',
            'category' => 'mascots-magic',
            'badge' => 'Family Favorite',
            'image_url' => 'assets/images/Fancy-magic-show-by-roppets-edutainment-production-inc.-1-265x265_c.png',
            'description' => 'Astonishing sleight-of-hand tricks, comedy magic, mind-boggling illusions, and cheerful audience participation suitable for all ages.',
            'starting_price' => 7500,
            'features' => "Professional Comedy Magician\nLive Dove & Rabbit Appearances\nStage Illusions & Mentalism\nCelebrant Special Magic Act"
        ],
        [
            'title' => 'Character Mascots & Hosting',
            'category' => 'mascots-magic',
            'badge' => 'Event Highlight',
            'image_url' => 'assets/images/finn_adventure.png',
            'description' => 'High-spirited mascot appearances and energetic party hosts to lead games, dance-alongs, cake ceremonies, and photo sessions.',
            'starting_price' => 6000,
            'features' => "Premium Lightweight Costumes\nEnergetic Party Host & DJ\nChoreographed Mascot Dance\nMeet & Greet Photo Session"
        ]
    ];
}

if (empty($activePackages)) {
    $activePackages = [
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

if (empty($approvedReviews)) {
    $approvedReviews = [
        [
            'client_name' => 'Carmela Santos',
            'location' => 'Quezon City',
            'event_type' => '7th Birthday Party',
            'rating' => 5,
            'comment' => 'The Bubble Show was the absolute highlight of our daughter\'s 7th birthday! The kids and even grandparents were in awe. Truly world-class entertainment!',
            'created_at' => '2026-08-15'
        ],
        [
            'client_name' => 'Atty. Victor Ramirez',
            'location' => 'Makati City',
            'event_type' => 'Corporate Family Day',
            'rating' => 5,
            'comment' => 'Roppets handled our company\'s 500-guest annual family day with flying colors. The puppet stories delivered heartfelt moral lessons. Highly recommended!',
            'created_at' => '2026-08-10'
        ],
        [
            'client_name' => 'Teacher Maricel Dela Cruz',
            'location' => 'Ateneo Grade School, QC',
            'event_type' => 'School Edutainment Tour',
            'rating' => 5,
            'comment' => 'The Teatro Itim Black Theater show left all our elementary students spellbound. Educational, breathtaking, and very organized production staff.',
            'created_at' => '2026-08-01'
        ]
    ];
}
?>

  <!-- Hero Slider Section -->
  <section class="hero-slider-section">
    <div class="slider-container">
      
      <!-- Slide 1: Bubble Show -->
      <div class="slide active">
        <img src="assets/images/hero_bubble.jpg" alt="The Bubble Factory Show" class="slide-bg" onerror="this.src='assets/images/The-original-bubble-show-by-the-bubble-factory-1-265x265_c.png'">
        <div class="container">
          <div class="slide-content">
            <span class="slide-tag"><i class="fa-solid fa-sparkles"></i> Spectacular Performance</span>
            <h1 class="slide-title">BUBBLE SHOW<br><span>BY THE BUBBLE FACTORY</span></h1>
            <p class="slide-desc">The original 3 all-girl performers in themed costumes featuring dynamic dance numbers, giant smoke bubbles, kid-in-a-bubble tricks, and magical choreography.</p>
            <div class="slide-buttons">
              <button type="button" class="btn btn-primary btn-lg" data-open-modal="booking" data-service="Bubble Show">
                <i class="fa-solid fa-calendar-check"></i> Book This Show
              </button>
              <a href="services.php#bubble" class="btn btn-white btn-lg">
                <i class="fa-solid fa-circle-info"></i> Learn More
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 2: Hand Puppet Show -->
      <div class="slide">
        <img src="assets/images/puppet_3pigs.jpg" alt="Roppets Hand Puppet Show" class="slide-bg" onerror="this.src='assets/images/smurfs-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png'">
        <div class="container">
          <div class="slide-content">
            <span class="slide-tag"><i class="fa-solid fa-masks-theater"></i> Classic Edutainment</span>
            <h2 class="slide-title">ROPPETS ORIGINAL<br><span>HAND PUPPET SHOW</span></h2>
            <p class="slide-desc">Over 100+ moral-rich and educational stories. Features 3 exciting puppet plays with our signature colorful portable theater stage, vibrant characters, and voice acting.</p>
            <div class="slide-buttons">
              <button type="button" class="btn btn-primary btn-lg" data-open-modal="booking" data-service="Hand Puppet Show">
                <i class="fa-solid fa-calendar-check"></i> Book Puppet Show
              </button>
              <a href="services.php#puppet" class="btn btn-white btn-lg">
                <i class="fa-solid fa-list-check"></i> View Stories
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 3: Teatro Itim Black Theater -->
      <div class="slide">
        <img src="assets/images/black_theater.jpg" alt="Teatro Itim Black Theater" class="slide-bg" onerror="this.src='assets/images/teatro-itim-by-black-theater-play-1-265x265_c.png'">
        <div class="container">
          <div class="slide-content">
            <span class="slide-tag"><i class="fa-solid fa-wand-magic-sparkles"></i> Best Seller Attraction</span>
            <h2 class="slide-title">TEATRO ITIM<br><span>BLACK THEATER PLAY</span></h2>
            <p class="slide-desc">A mesmerizing ultraviolet glow-in-the-dark puppetry style that delights both kids and adults. Full-stage fluorescent visual effects and storytelling innovation.</p>
            <div class="slide-buttons">
              <button type="button" class="btn btn-primary btn-lg" data-open-modal="booking" data-service="Teatro Itim">
                <i class="fa-solid fa-calendar-check"></i> Inquire Now
              </button>
              <a href="services.php#teatro-itim" class="btn btn-white btn-lg">
                <i class="fa-solid fa-eye"></i> Explore Black Theater
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Slider Arrows -->
      <button class="slider-arrow prev" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
      <button class="slider-arrow next" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>

      <!-- Slider Dots -->
      <div class="slider-dots">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
      </div>
    </div>
  </section>

  <!-- Value Proposition & Mission -->
  <section class="section-padding" style="background: #ffffff;">
    <div class="container">
      <div class="intro-mission-card">
        <span class="section-badge">Welcome to Roppets</span>
        <h2 style="font-size: 28px; margin: 10px 0 14px 0; color: var(--primary);">We Value Education through Entertainment</h2>
        <p class="intro-mission-text">
          <strong>Roppets Edutainment Production Inc.</strong> has been giving the art of puppetry a taste of innovation with its high-performing edutainment methods. We instill <strong>social, moral, academic, environmental, and spiritual values</strong> into every performance to inspire the minds of children and audiences across the Philippines.
        </p>
      </div>

      <div class="section-header">
        <span class="section-badge">Why Choose Roppets</span>
        <h2 class="section-title">The Edutainment Advantage</h2>
        <p class="section-subtitle">Over three decades of excellence in children's events, school tours, and custom fabrication.</p>
      </div>

      <div class="why-grid">
        <div class="why-card">
          <div class="why-icon"><i class="fa-solid fa-award"></i></div>
          <h3>Reputation & Heritage</h3>
          <p>Over 36 years of trusted service. Proud member of <em>Samahan ng mga Papetir ng Pilipinas</em>, <em>Puppeteers of America</em>, and <em>ASEAN Puppetry Association</em>.</p>
        </div>

        <div class="why-card">
          <div class="why-icon"><i class="fa-solid fa-shield-heart"></i></div>
          <h3>Masterful Quality</h3>
          <p>Hand-crafted puppets, trained professional puppeteers, captivating stage sets, and award-winning bubble and black theater choreography.</p>
        </div>

        <div class="why-card">
          <div class="why-icon"><i class="fa-solid fa-tags"></i></div>
          <h3>Competitive Pricing</h3>
          <p>Flexible and transparent entertainment packages designed for kiddie birthdays, schools, malls, corporate family days, and custom fabrication.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Services & Entertainment Catalog -->
  <section class="section-padding services-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Our Entertainment Offerings</span>
        <h2 class="section-title">Children's Shows & Special Acts</h2>
        <p class="section-subtitle">Choose from our flagship shows designed to keep audiences captivated from start to finish.</p>
      </div>

      <!-- Service Filter Tabs -->
      <div class="service-tabs">
        <button type="button" class="tab-btn active" data-filter="all">All Services</button>
        <button type="button" class="tab-btn" data-filter="puppetry">Puppet Shows</button>
        <button type="button" class="tab-btn" data-filter="specialty">Specialty Shows</button>
        <button type="button" class="tab-btn" data-filter="mascots-magic">Mascots & Magic</button>
      </div>

      <div class="services-grid">
        <?php if (!empty($activeServices)): ?>
          <?php foreach ($activeServices as $s): 
            $cat = $s['category'] ?? 'all';
            $title = $s['title'] ?? 'Show';
            $desc = $s['description'] ?? '';
            $badge = $s['badge'] ?? '';
            $imgUrl = $s['image_url'] ?? 'assets/images/logo.png';
            $price = floatval($s['starting_price'] ?? 0);
          ?>
            <div class="service-card" data-category="<?php echo htmlspecialchars($cat); ?>">
              <div class="service-img-wrap">
                <?php if (!empty($badge)): ?>
                  <span class="service-badge"><?php echo htmlspecialchars($badge); ?></span>
                <?php endif; ?>
                <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($title); ?>" onerror="this.src='assets/images/logo.png'">
              </div>
              <div class="service-body">
                <h3><?php echo htmlspecialchars($title); ?></h3>
                <p><?php echo htmlspecialchars($desc); ?></p>
                <?php if (!empty($s['features'])): ?>
                  <ul class="service-features">
                    <?php 
                      $featList = explode("\n", $s['features']);
                      foreach ($featList as $f): 
                        $f = trim($f);
                        if (empty($f)) continue;
                    ?>
                      <li><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($f); ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                <div class="service-footer">
                  <span class="service-price">From ₱<?php echo number_format($price, 0); ?></span>
                  <button type="button" class="btn btn-primary btn-sm" data-open-modal="booking" data-service="<?php echo htmlspecialchars($title); ?>">Book Now</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; color: var(--muted-text); width: 100%;">No entertainment services currently listed.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Statistics Animated Counter -->
  <section class="stats-section">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-icon"><i class="fa-solid fa-face-smile"></i></div>
          <div class="stat-number" data-target="50000" data-suffix="+">0</div>
          <div class="stat-label">Happy Kids & Guests</div>
        </div>

        <div class="stat-item">
          <div class="stat-icon"><i class="fa-solid fa-masks-theater"></i></div>
          <div class="stat-number" data-target="900" data-suffix="+">0</div>
          <div class="stat-label">Events & Shows Performed</div>
        </div>

        <div class="stat-item">
          <div class="stat-icon"><i class="fa-solid fa-building-flag"></i></div>
          <div class="stat-number" data-target="50" data-suffix="+">0</div>
          <div class="stat-label">Corporate & Mall Partners</div>
        </div>

        <div class="stat-item">
          <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
          <div class="stat-number" data-target="36" data-suffix="+">0</div>
          <div class="stat-label">Years of Edutainment Heritage</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Custom Mascot & Plush Fabrication Spotlight -->
  <section class="section-padding fabrication-section">
    <div class="container">
      <div class="fabrication-banner">
        <div class="fabrication-text">
          <span class="section-badge">Custom Fabrication Studio</span>
          <h2>Corporate Mascot & Custom Plush Construction</h2>
          <p>
            Looking for a custom mascot for your brand, school, or corporate identity? We are the Philippines' trusted fabrication workshop for high-durability mascots, custom hand puppets, and batch plush stuffed toys.
          </p>
          <ul class="fabrication-list">
            <li><i class="fa-solid fa-check-circle"></i> Custom Corporate Mascots</li>
            <li><i class="fa-solid fa-check-circle"></i> Hand & Giant Puppets (up to 10ft)</li>
            <li><i class="fa-solid fa-check-circle"></i> Premium Plush Toys & Merchandise</li>
            <li><i class="fa-solid fa-check-circle"></i> Durable Lightweight Materials</li>
          </ul>
          <div style="display: flex; gap: 14px; flex-wrap: wrap;">
            <a href="fabrication.php" class="btn btn-primary">
              <i class="fa-solid fa-scissors"></i> Explore Fabrication Services
            </a>
            <button type="button" class="btn btn-outline" data-open-modal="booking" data-service="Mascot Fabrication">
              <i class="fa-solid fa-file-invoice"></i> Request Fabrication Quote
            </button>
          </div>
        </div>

        <div class="fabrication-images">
          <div class="fab-img-card">
            <img src="assets/images/doc_mcstuffins.jpg" alt="Doc McStuffins Puppet">
            <span>Doc McStuffins Puppet</span>
          </div>
          <div class="fab-img-card">
            <img src="assets/images/finn_adventure.png" alt="Adventure Time Finn Mascot">
            <span>Finn Character Mascot</span>
          </div>
          <div class="fab-img-card">
            <img src="assets/images/Mickey-mouse-clubhouse-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png" alt="Clubhouse Puppet">
            <span>Custom Hand Puppets</span>
          </div>
          <div class="fab-img-card">
            <img src="assets/images/table-puppetry-1-265x265_c.png" alt="Table Puppetry">
            <span>Giant & Table Puppets</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Party Packages Section -->
  <section class="section-padding packages-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Party Packages</span>
        <h2 class="section-title">Value-Packed Event Packages</h2>
        <p class="section-subtitle">Complete edutainment solutions for memorable children's birthday celebrations and school events.</p>
      </div>

      <div class="packages-grid">
        <?php if (!empty($activePackages)): ?>
          <?php foreach ($activePackages as $p): 
            $isPop = !empty($p['is_popular']);
            $pName = $p['name'] ?? 'Package';
            $pPrice = floatval($p['price'] ?? 0);
          ?>
            <div class="package-card <?php echo $isPop ? 'popular' : ''; ?>">
              <?php if ($isPop): ?>
                <span class="popular-badge">Most Popular Choice</span>
              <?php endif; ?>
              <div class="package-header">
                <h3 class="package-name"><?php echo htmlspecialchars($pName); ?></h3>
                <?php if (!empty($p['subtitle'])): ?>
                  <p class="package-subtitle"><?php echo htmlspecialchars($p['subtitle']); ?></p>
                <?php endif; ?>
                <div class="package-price">₱<?php echo number_format($pPrice, 0); ?> <small>/ event</small></div>
              </div>
              <?php if (!empty($p['features'])): ?>
                <ul class="package-features">
                  <?php 
                    $pkgFeats = explode("\n", $p['features']);
                    foreach ($pkgFeats as $pf):
                      $pf = trim($pf);
                      if (empty($pf)) continue;
                  ?>
                    <li><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($pf); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <button type="button" class="btn <?php echo $isPop ? 'btn-primary' : 'btn-outline'; ?>" data-open-modal="booking" data-service="<?php echo htmlspecialchars($pName); ?>">
                Book <?php echo htmlspecialchars($pName); ?>
              </button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; color: var(--muted-text); width: 100%;">No party packages currently listed.</p>
        <?php endif; ?>
      </div>

      <div style="text-align: center; margin-top: 40px;">
        <a href="packages.php" class="btn btn-secondary btn-lg">
          <i class="fa-solid fa-file-pdf"></i> View All Packages & Full Pricelist
        </a>
      </div>
    </div>
  </section>

  <!-- Media Features & Trust Badges -->
  <section class="trust-section">
    <div class="container">
      <h3 class="trust-title">As Featured On Leading National Media</h3>
      <div class="logos-grid" style="margin-bottom: 40px;">
        <div class="logo-item"><img src="assets/images/media_ukg.png" alt="Umagang Kay Ganda"></div>
        <div class="logo-item"><img src="assets/images/media_24oras.png" alt="24 Oras"></div>
        <div class="logo-item"><img src="assets/images/media_saksi.png" alt="Saksi"></div>
        <div class="logo-item"><img src="assets/images/media_tfc.png" alt="The Filipino Channel (TFC)"></div>
        <div class="logo-item"><img src="assets/images/client_abscbn.png" alt="ABS-CBN"></div>
        <div class="logo-item"><img src="assets/images/client_gma.png" alt="GMA Network"></div>
      </div>

      <h3 class="trust-title" style="margin-top: 20px;">Trusted By Leading Brands & Malls</h3>
      <div class="logos-grid">
        <div class="logo-item"><img src="assets/images/client_smmalls.png" alt="SM Supermalls"></div>
        <div class="logo-item"><img src="assets/images/client_robinsons.png" alt="Robinsons Land Corporation"></div>
        <div class="logo-item"><img src="assets/images/client_megaworld.png" alt="Megaworld"></div>
        <div class="logo-item"><img src="assets/images/client_hyundai.png" alt="Hyundai"></div>
        <div class="logo-item"><img src="assets/images/client_nestle.png" alt="Nestle"></div>
        <div class="logo-item"><img src="assets/images/client_gsk.png" alt="GSK"></div>
        <div class="logo-item"><img src="assets/images/client_rcbc.png" alt="RCBC Bank"></div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="section-padding testimonials-section">
    <div class="container">
      <div class="section-header" style="position: relative;">
        <span class="section-badge">Client Testimonials</span>
        <h2 class="section-title">What Our Customers Say</h2>
        <p class="section-subtitle">Read reviews from happy parents, school administrators, and corporate event organizers.</p>
        <div style="margin-top: 15px;">
          <button type="button" class="btn btn-outline btn-sm" data-open-modal="review" style="border-radius: 20px; font-size: 13px;">
            <i class="fa-solid fa-pen-fancy" style="color: var(--primary); margin-right: 6px;"></i> Share Your Roppets Experience
          </button>
        </div>
      </div>

      <div class="testimonials-grid">
        <?php if (!empty($approvedReviews)): ?>
          <?php foreach ($approvedReviews as $r): 
            $initials = '';
            $clientName = trim($r['client_name'] ?? 'Client');
            $names = explode(' ', $clientName);
            foreach ($names as $n) {
              if (!empty($n)) $initials .= strtoupper(substr($n, 0, 1));
            }
            $initials = substr($initials, 0, 2) ?: 'RP';
            $rating = intval($r['rating'] ?? 5);
            $reviewText = $r['comment'] ?? ($r['review_text'] ?? '');
            $locationOrType = !empty($r['location']) ? $r['location'] : (!empty($r['event_type']) ? $r['event_type'] : 'Verified Client');
          ?>
            <div class="testimonial-card">
              <div>
                <div class="stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-solid fa-star" style="<?php echo ($i <= $rating) ? 'color: #f59e0b;' : 'color: #cbd5e1;'; ?>"></i>
                  <?php endfor; ?>
                </div>
                <p class="testimonial-text">
                  "<?php echo htmlspecialchars($reviewText); ?>"
                </p>
              </div>
              <div class="client-info">
                <div class="client-avatar"><?php echo htmlspecialchars($initials); ?></div>
                <div class="client-details">
                  <h4><?php echo htmlspecialchars($clientName); ?></h4>
                  <span><?php echo htmlspecialchars($locationOrType); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; color: var(--muted-text); width: 100%;">No approved client testimonials yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- CTA Download Banner -->
  <section class="cta-banner-section">
    <div class="container">
      <div class="cta-banner-content">
        <div class="cta-text">
          <h2>Ready to Create Magical Memories?</h2>
          <p>Download our official complete pricelist and packages or get in touch directly for customized event quotations.</p>
        </div>
        <div class="cta-actions">
          <button type="button" class="btn btn-secondary btn-lg" data-open-modal="booking">
            <i class="fa-solid fa-calendar-star"></i> Book an Event Now
          </button>
          <a href="packages.php" class="btn btn-white btn-lg">
            <i class="fa-solid fa-download"></i> View Full Pricelist
          </a>
        </div>
      </div>
    </div>
  </section>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
