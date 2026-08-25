<?php
$pageTitle = "Children's Entertainment & Shows | Roppets Edutainment Production Inc.";
$currentNav = "services";
include_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$pdo = getDBConnection();
$activeServices = [];
$activeStories = [];
$serviceMap = [];
try {
    $activeServices = $pdo->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY sort_order ASC, id ASC")->fetchAll();
    $activeStories = $pdo->query("SELECT * FROM puppet_stories WHERE is_active = TRUE ORDER BY id ASC")->fetchAll();
    foreach ($activeServices as $srv) {
        $serviceMap[$srv['slug']] = $srv;
    }
} catch (Exception $e) {}

function findSrv($map, $key, $defaultTitle, $defaultPrice, $defaultDesc = '') {
    if (isset($map[$key])) {
        $s = $map[$key];
    } else {
        $s = null;
        foreach ($map as $k => $v) {
            if (strpos($k, $key) !== false || strpos($key, $k) !== false) {
                $s = $v;
                break;
            }
        }
    }
    if ($s) {
        return [
            'title' => $s['title'],
            'price' => '₱' . number_format($s['starting_price'], 0),
            'starting_price' => $s['starting_price'],
            'description' => $s['description'] ?: $defaultDesc,
            'image_url' => $s['image_url'],
            'features' => $s['features'],
            'badge' => $s['badge']
        ];
    }
    return [
        'title' => $defaultTitle,
        'price' => '₱' . number_format($defaultPrice, 0),
        'starting_price' => $defaultPrice,
        'description' => $defaultDesc,
        'image_url' => '',
        'features' => '',
        'badge' => ''
    ];
}

$srvPuppet = findSrv($serviceMap, 'puppet', 'The Original Roppets Hand Puppet Show', 4500);
$srvBubble = findSrv($serviceMap, 'bubble', 'Bubble Show by The Bubble Factory', 6000);
$srvTeatro = findSrv($serviceMap, 'teatro', 'Teatro Itim (Black Theater Play)', 7500);
$srvMarionette = findSrv($serviceMap, 'marionette', 'Cultural Marionette (Tali Galaw)', 5500);
$srvMascot = findSrv($serviceMap, 'mascot', 'Character Mascots', 3500);
$srvMagic = findSrv($serviceMap, 'magic', 'Fancy Magic & Illusion', 4000);
?>

  <!-- Page Header -->
  <div style="background: linear-gradient(135deg, #1b1622 0%, #2f1d3a 100%); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
      <span class="section-badge" style="background: rgba(255,255,255,0.15); color: #fff;">World-Class Edutainment</span>
      <h1 style="font-size: 40px; color: #fff; margin-top: 10px;">Children's Entertainment & Shows</h1>
      <p style="color: #cbd5e1; max-width: 700px; margin: 10px auto 0 auto; font-size: 16px;">
        Over 36 years of master puppetry, spectacular bubble choreography, black theater visual arts, and family-friendly magic.
      </p>
    </div>
  </div>

  <!-- Service Detail 1: Hand Puppet Show -->
  <section class="section-padding" id="puppet" style="background: #ffffff;">
    <div class="container">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
        <div>
          <span class="section-badge"><?php echo htmlspecialchars($srvPuppet['badge'] ?: 'Flagship Performance'); ?></span>
          <h2 style="font-size: 32px; margin: 10px 0 16px 0;"><?php echo htmlspecialchars($srvPuppet['title']); ?></h2>
          <p style="color: var(--muted-text); margin-bottom: 16px;">
            <?php echo htmlspecialchars($srvPuppet['description'] ?: 'Roppets hand puppet shows are designed to instill values while providing vibrant laughter. Each booking includes our signature theater backdrop, complete sound system, and 3 full moral puppet plays.'); ?>
          </p>
          
          <h4 style="font-size: 17px; margin-bottom: 10px; color: var(--primary);">Popular Moral Story Plays:</h4>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 25px;">
            <?php if (!empty($activeStories)): ?>
              <?php foreach ($activeStories as $st): ?>
                <div style="font-size: 13.5px;"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 6px;"></i> <?php echo htmlspecialchars($st['title']); ?></div>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="font-size: 13.5px;"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 6px;"></i> The 3 Little Pigs</div>
              <div style="font-size: 13.5px;"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 6px;"></i> Little Red Riding Hood</div>
              <div style="font-size: 13.5px;"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 6px;"></i> Pinocchio & Honest Heart</div>
              <div style="font-size: 13.5px;"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 6px;"></i> The Boy Who Cried Wolf</div>
              <div style="font-size: 13.5px;"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 6px;"></i> The Turtle & The Monkey</div>
            <?php endif; ?>
            <div style="font-size: 13.5px; font-weight: 600; color: var(--primary);"><i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 6px;"></i> 100+ Custom Story Titles</div>
          </div>

          <div style="display: flex; gap: 14px; align-items: center;">
            <button type="button" class="btn btn-primary" data-open-modal="booking" data-service="<?php echo htmlspecialchars($srvPuppet['title']); ?>">
              <i class="fa-solid fa-calendar-plus"></i> Book Puppet Show
            </button>
            <span style="font-family: var(--font-heading); font-weight: 700; color: var(--primary); font-size: 18px;">Rate: <?php echo $srvPuppet['price']; ?></span>
          </div>
        </div>

        <div>
          <img src="<?php echo htmlspecialchars($srvPuppet['image_url'] ?: 'assets/images/puppet_3pigs.jpg'); ?>" alt="Hand Puppet Stage" style="border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg);" onerror="this.src='assets/images/smurfs-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png'">
        </div>
      </div>
    </div>
  </section>

  <!-- Service Detail 2: The Bubble Show -->
  <section class="section-padding" id="bubble" style="background: #fdfafd;">
    <div class="container">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
        <div style="order: 2;">
          <span class="section-badge"><?php echo htmlspecialchars($srvBubble['badge'] ?: 'Visual Spectacle'); ?></span>
          <h2 style="font-size: 32px; margin: 10px 0 16px 0;"><?php echo htmlspecialchars($srvBubble['title']); ?></h2>
          <p style="color: var(--muted-text); margin-bottom: 16px;">
            <?php echo htmlspecialchars($srvBubble['description'] ?: 'The Bubble Factory features 3 all-girl performers dressed in stunning themed costumes performing synchronized bubble tricks, bubble sculptures, smoke-filled bouncing bubbles, and putting children inside gigantic soap bubbles!'); ?>
          </p>

          <ul style="margin-bottom: 25px;">
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> 3 All-Girl Performers in Themed Outfits</li>
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> Giant Smoke Bubble Volcano & Hand Sculptures</li>
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> Celebrant & Guests Inside a Giant Bubble</li>
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> High-Energy Music & Choreography</li>
          </ul>

          <div style="display: flex; gap: 14px; align-items: center;">
            <button type="button" class="btn btn-primary" data-open-modal="booking" data-service="<?php echo htmlspecialchars($srvBubble['title']); ?>">
              <i class="fa-solid fa-calendar-plus"></i> Book Bubble Show
            </button>
            <span style="font-family: var(--font-heading); font-weight: 700; color: var(--primary); font-size: 18px;">Rate: <?php echo $srvBubble['price']; ?></span>
          </div>
        </div>

        <div style="order: 1;">
          <img src="<?php echo htmlspecialchars($srvBubble['image_url'] ?: 'assets/images/hero_bubble.jpg'); ?>" alt="Bubble Show" style="border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg);" onerror="this.src='assets/images/The-original-bubble-show-by-the-bubble-factory-1-265x265_c.png'">
        </div>
      </div>
    </div>
  </section>

  <!-- Service Detail 3: Teatro Itim Black Theater -->
  <section class="section-padding" id="teatro-itim" style="background: #ffffff;">
    <div class="container">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
        <div>
          <span class="section-badge"><?php echo htmlspecialchars($srvTeatro['badge'] ?: 'Glow-in-the-Dark Wonder'); ?></span>
          <h2 style="font-size: 32px; margin: 10px 0 16px 0;"><?php echo htmlspecialchars($srvTeatro['title']); ?></h2>
          <p style="color: var(--muted-text); margin-bottom: 16px;">
            <?php echo htmlspecialchars($srvTeatro['description'] ?: 'A Czech-inspired black light theatre technique popularized in the Philippines exclusively by Roppets. Under UV lighting, invisible puppeteers dressed in black velvet make colorful fluorescent puppets fly, transform, and dance across the stage.'); ?>
          </p>

          <ul style="margin-bottom: 25px;">
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> Complete Darkened Stage Set & Ultraviolet (UV) Lights</li>
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> Floating Glow-in-the-Dark Puppets & Optical Illusions</li>
            <li style="margin-bottom: 8px;"><i class="fa-solid fa-circle-check" style="color: var(--accent-green); margin-right: 8px;"></i> Inspiring Musical Soundtrack & Narration</li>
          </ul>

          <div style="display: flex; gap: 14px; align-items: center;">
            <button type="button" class="btn btn-primary" data-open-modal="booking" data-service="<?php echo htmlspecialchars($srvTeatro['title']); ?>">
              <i class="fa-solid fa-calendar-plus"></i> Book Black Theater
            </button>
            <span style="font-family: var(--font-heading); font-weight: 700; color: var(--primary); font-size: 18px;">Rate: <?php echo $srvTeatro['price']; ?></span>
          </div>
        </div>

        <div>
          <img src="<?php echo htmlspecialchars($srvTeatro['image_url'] ?: 'assets/images/black_theater.jpg'); ?>" alt="Teatro Itim Black Theater" style="border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg);" onerror="this.src='assets/images/teatro-itim-by-black-theater-play-2-265x265_c.png'">
        </div>
      </div>
    </div>
  </section>

  <!-- Service Detail 4: Marionettes & Mascots Grid -->
  <section class="section-padding" id="marionette" style="background: #fcf8fc;">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">More Specialty Attractions</span>
        <h2 class="section-title">Marionettes, Mascots & Magic</h2>
      </div>

      <div class="services-grid">
        <!-- Cultural Marionette -->
        <div class="service-card">
          <div class="service-img-wrap">
            <img src="<?php echo htmlspecialchars($srvMarionette['image_url'] ?: 'assets/images/Tali-Galaw-Marionette-Group-4-265x265_c.png'); ?>" alt="Tali Galaw" onerror="this.src='assets/images/logo.png'">
          </div>
          <div class="service-body">
            <h3><?php echo htmlspecialchars($srvMarionette['title']); ?></h3>
            <p><?php echo htmlspecialchars($srvMarionette['description'] ?: 'Traditional string marionettes performing Philippine cultural dances (Tinikling, Singkil, Maglalatik) with live master puppeteers.'); ?></p>
            <div class="service-footer">
              <span class="service-price"><?php echo $srvMarionette['price']; ?></span>
              <button type="button" class="btn btn-primary btn-sm" data-open-modal="booking" data-service="<?php echo htmlspecialchars($srvMarionette['title']); ?>">Book</button>
            </div>
          </div>
        </div>

        <!-- Character Mascots -->
        <div class="service-card" id="mascots">
          <div class="service-img-wrap">
            <img src="<?php echo htmlspecialchars($srvMascot['image_url'] ?: 'assets/images/finn_adventure.png'); ?>" alt="Character Mascots" onerror="this.src='assets/images/logo.png'">
          </div>
          <div class="service-body">
            <h3><?php echo htmlspecialchars($srvMascot['title']); ?></h3>
            <p><?php echo htmlspecialchars($srvMascot['description'] ?: 'Life-size friendly mascots for meet-and-greets, grand entrances, choreographed dance routines, and birthday cake ceremonies.'); ?></p>
            <div class="service-footer">
              <span class="service-price"><?php echo $srvMascot['price']; ?> <small>/ mascot</small></span>
              <button type="button" class="btn btn-primary btn-sm" data-open-modal="booking" data-service="<?php echo htmlspecialchars($srvMascot['title']); ?>">Book</button>
            </div>
          </div>
        </div>

        <!-- Fancy Magic Show -->
        <div class="service-card" id="magic">
          <div class="service-img-wrap">
            <img src="<?php echo htmlspecialchars($srvMagic['image_url'] ?: 'assets/images/Fancy-magic-show-by-roppets-edutainment-production-inc.-2-265x265_c.png'); ?>" alt="Magic Show" onerror="this.src='assets/images/logo.png'">
          </div>
          <div class="service-body">
            <h3><?php echo htmlspecialchars($srvMagic['title']); ?></h3>
            <p><?php echo htmlspecialchars($srvMagic['description'] ?: 'Interactive, laugh-out-loud comedy magic routines featuring doves, illusions, and celebrant participation.'); ?></p>
            <div class="service-footer">
              <span class="service-price"><?php echo $srvMagic['price']; ?></span>
              <button type="button" class="btn btn-primary btn-sm" data-open-modal="booking" data-service="<?php echo htmlspecialchars($srvMagic['title']); ?>">Book</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
