<?php
$pageTitle = "Event Photo & Video Gallery | Roppets Edutainment Production Inc.";
$currentNav = "gallery";
include_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$pdo = getDBConnection();
$galleryItems = [];
try {
    $galleryItems = $pdo->query("SELECT * FROM gallery_items ORDER BY is_featured DESC, id ASC")->fetchAll();
} catch (Exception $e) {}
?>

  <!-- Page Header -->
  <div style="background: linear-gradient(135deg, #1b1622 0%, #2f1d3a 100%); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
      <span class="section-badge" style="background: rgba(255,255,255,0.15); color: #fff;">Media Highlights</span>
      <h1 style="font-size: 40px; color: #fff; margin-top: 10px;">Event Photo & Video Gallery</h1>
      <p style="color: #cbd5e1; max-width: 700px; margin: 10px auto 0 auto; font-size: 16px;">
        Catch a glimpse of the smiles, giant bubble magic, fluorescent black theater puppetry, and corporate mascots crafted by Roppets.
      </p>
    </div>
  </div>

  <section class="section-padding" style="background: #ffffff;">
    <div class="container">
      
      <!-- Gallery Filter Tabs -->
      <div class="service-tabs" style="margin-bottom: 40px;">
        <button type="button" class="tab-btn active" data-filter="all">All Highlights</button>
        <button type="button" class="tab-btn" data-filter="puppetry">Puppet Shows</button>
        <button type="button" class="tab-btn" data-filter="bubble">Bubble Factory</button>
        <button type="button" class="tab-btn" data-filter="black-theater">Teatro Itim</button>
        <button type="button" class="tab-btn" data-filter="mascots">Mascots & Fabrication</button>
      </div>

      <!-- Gallery Grid -->
      <div class="services-grid" id="mediaGalleryGrid">
        <?php foreach ($galleryItems as $item): ?>
          <div class="service-card" data-category="<?php echo htmlspecialchars($item['category']); ?>">
            <div class="service-img-wrap" style="height: 240px;">
              <img src="<?php echo htmlspecialchars($item['media_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" onerror="this.src='assets/images/logo.png'">
              <?php if ($item['is_featured']): ?>
                <span class="service-badge" style="background: #f59e0b; color: #fff;">Featured</span>
              <?php endif; ?>
            </div>
            <div class="service-body" style="padding: 20px;">
              <h3 style="font-size: 17px; margin-bottom: 6px;"><?php echo htmlspecialchars($item['title']); ?></h3>
              <p style="color: var(--muted-text); font-size: 13.5px; margin-bottom: 12px;">
                <?php echo htmlspecialchars($item['caption'] ?: 'Roppets Edutainment live production showcase.'); ?>
              </p>
              <span class="badge" style="background: rgba(133,46,112,0.1); color: var(--primary); font-size: 11px; align-self: flex-start;">
                <?php echo strtoupper(htmlspecialchars($item['category'])); ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="text-align: center; margin-top: 50px;">
        <button type="button" class="btn btn-primary btn-lg" data-open-modal="booking">
          <i class="fa-solid fa-calendar-star"></i> Book Your Event with Roppets
        </button>
      </div>

    </div>
  </section>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
