<?php
if (!isset($pageTitle)) {
    $pageTitle = "Roppets Edutainment Production Inc. | Puppet Shows, Bubble Shows & Mascots";
}
if (!isset($currentNav)) {
    $currentNav = "home";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="Roppets Edutainment Production Inc. - Philippines' premier educational entertainment provider. Puppet shows, bubble shows, mascot fabrication, black theater, and party entertainment.">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/logo.png">

  <!-- Font Awesome 6 Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Top Bar -->
  <div class="top-bar">
    <div class="container">
      <div class="top-bar-info">
        <a href="tel:+63289123456"><i class="fa-solid fa-phone"></i> (02) 8912-3456 / +63 917 530 0823</a>
        <a href="mailto:edutainment@roppets.com"><i class="fa-solid fa-envelope"></i> edutainment@roppets.com</a>
        <span><i class="fa-solid fa-location-dot"></i> Cubao, Quezon City, Metro Manila</span>
      </div>
      <div class="top-bar-socials">
        <a href="https://www.facebook.com/RoppetsEdutainmentProduction/" target="_blank" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://www.youtube.com/" target="_blank" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
        <a href="https://www.instagram.com/" target="_blank" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="track.php" title="Track Booking" style="width: auto; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; color: #fff; background: var(--primary);"><i class="fa-solid fa-magnifying-glass"></i> Track</a>
      </div>
    </div>
  </div>

  <!-- Main Navigation Header -->
  <header class="site-header">
    <div class="container">
      <a href="index.php" class="brand-logo">
        <img src="assets/images/logo.png" alt="Roppets Edutainment Production Inc.">
      </a>

      <nav class="main-nav">
        <ul class="nav-menu">
          <li class="nav-item <?php echo ($currentNav === 'home') ? 'active' : ''; ?>">
            <a href="index.php" class="nav-link">Home</a>
          </li>

          <li class="nav-item has-dropdown <?php echo ($currentNav === 'services') ? 'active' : ''; ?>">
            <a href="services.php" class="nav-link">
              Children's Entertainment <i class="fa-solid fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
              <li><a href="services.php#puppet"><i class="fa-solid fa-masks-theater"></i> Hand Puppet Shows</a></li>
              <li><a href="services.php#bubble"><i class="fa-solid fa-soap"></i> Bubble Show by Bubble Factory</a></li>
              <li><a href="services.php#teatro-itim"><i class="fa-solid fa-wand-magic-sparkles"></i> Teatro Itim Black Theater</a></li>
              <li><a href="services.php#marionette"><i class="fa-solid fa-person-walking"></i> Marionette (Tali Galaw)</a></li>
              <li><a href="services.php#mascots"><i class="fa-solid fa-child-reaching"></i> Character Mascots</a></li>
              <li><a href="services.php#magic"><i class="fa-solid fa-hat-wizard"></i> Magic & Illusion Shows</a></li>
            </ul>
          </li>

          <li class="nav-item has-dropdown <?php echo ($currentNav === 'fabrication') ? 'active' : ''; ?>">
            <a href="fabrication.php" class="nav-link">
              Custom Plush & Mascot <i class="fa-solid fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
              <li><a href="fabrication.php#mascots"><i class="fa-solid fa-paw"></i> Corporate Mascot Fabrication</a></li>
              <li><a href="fabrication.php#stuffed"><i class="fa-solid fa-shapes"></i> Stuffed Toy Making & Plush</a></li>
              <li><a href="fabrication.php#puppets"><i class="fa-solid fa-hand-holding-heart"></i> Puppet Making & Fabrication</a></li>
              <li><a href="fabrication.php#sales"><i class="fa-solid fa-tag"></i> Puppets for Sale & Souvenirs</a></li>
            </ul>
          </li>

          <li class="nav-item <?php echo ($currentNav === 'packages') ? 'active' : ''; ?>">
            <a href="packages.php" class="nav-link">Party Packages</a>
          </li>

          <li class="nav-item <?php echo ($currentNav === 'contact') ? 'active' : ''; ?>">
            <a href="contact.php" class="nav-link">Contact Us</a>
          </li>
        </ul>
      </nav>

      <div class="header-actions">
        <button type="button" class="btn btn-primary btn-sm" data-open-modal="booking">
          <i class="fa-solid fa-calendar-check"></i> Inquire / Book
        </button>
        <button type="button" class="mobile-toggle" aria-label="Toggle Navigation">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>
  </header>
