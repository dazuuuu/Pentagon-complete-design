<?php
/**
 * Pentagon Quest — Destinations Page (Modern Redesign)
 */
$page_title       = 'Safari Destinations — Kenya, Tanzania, Uganda, Rwanda & Beyond';
$page_description = 'Explore Pentagon Quest\'s safari destinations across Africa. From Kenya\'s Masai Mara and Tanzania\'s Serengeti to Uganda\'s gorilla forests and Rwanda\'s volcanic highlands — discover your perfect African adventure.';
$current_page     = 'destinations.php';
$base_path        = '';
include 'includes/header.php';

// Tour data array
$tours = [
  ['id' => 1, 'title' => 'Masai Mara Great Migration Safari', 'dest' => 'Kenya', 'type' => 'Wildlife Safari', 'dur' => '7 Days', 'price' => '$1,850', 'badge' => 'Best Seller'],
  ['id' => 2, 'title' => 'Serengeti & Ngorongoro Crater', 'dest' => 'Tanzania', 'type' => 'Wildlife Safari', 'dur' => '9 Days', 'price' => '$2,200', 'badge' => 'Popular'],
  ['id' => 3, 'title' => 'Bwindi Gorilla Trekking Expedition', 'dest' => 'Uganda', 'type' => 'Gorilla Trekking', 'dur' => '5 Days', 'price' => '$2,400', 'badge' => 'Adventure'],
  ['id' => 4, 'title' => 'Kilimanjaro Summit — Machame Route', 'dest' => 'Tanzania', 'type' => 'Mountain Trek', 'dur' => '8 Days', 'price' => '$2,100', 'badge' => 'Trekking'],
  ['id' => 5, 'title' => 'Rwanda Gorillas & Volcanoes', 'dest' => 'Rwanda', 'type' => 'Gorilla Trekking', 'dur' => '4 Days', 'price' => '$2,800', 'badge' => 'Exclusive'],
  ['id' => 6, 'title' => 'Zanzibar Beach & Spice Retreat', 'dest' => 'Tanzania', 'type' => 'Beach & Coastal', 'dur' => '6 Days', 'price' => '$1,400', 'badge' => 'Relaxation'],
  ['id' => 7, 'title' => 'Amboseli & Tsavo Safari Circuit', 'dest' => 'Kenya', 'type' => 'Wildlife Safari', 'dur' => '6 Days', 'price' => '$1,650', 'badge' => 'Value'],
  ['id' => 8, 'title' => 'Victoria Falls & Botswana Safari', 'dest' => 'Botswana', 'type' => 'Wildlife Safari', 'dur' => '10 Days', 'price' => '$3,200', 'badge' => 'Premium'],
  ['id' => 9, 'title' => 'Namibia Desert & Sossusvlei Dunes', 'dest' => 'Namibia', 'type' => 'Wildlife Safari', 'dur' => '12 Days', 'price' => '$3,600', 'badge' => 'Luxury'],
];
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Explore the Continent</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Safari Destinations</h1>
    
    <!-- Search Bar in Hero -->
    <div class="search-row-wrap reveal mt-5">
      <div class="search-field">
        <label>Destination</label>
        <select><option>All Countries</option></select>
      </div>
      <div class="search-field">
        <label>Tour Type</label>
        <select><option>All Types</option></select>
      </div>
      <div class="search-field">
        <label>Budget</label>
        <select><option>Any Budget</option></select>
      </div>
      <button class="btn-search-go">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
    </div>
  </div>
</section>

<!-- Tours Grid -->
<section class="section-pad">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($tours as $tour): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: var(--transition);" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
          <div style="height: 230px; background: var(--green); position: relative;">
             <svg width="100%" height="100%" viewBox="0 0 400 230" style="opacity: 0.8;">
               <rect width="400" height="230" fill="var(--green)"/>
               <path d="M0,230 Q100,160 200,200 Q300,240 400,180 L400,230 L0,230 Z" fill="var(--green-light)"/>
             </svg>
             <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo $tour['badge']; ?></span>
          </div>
          <div style="padding: 24px;">
            <div class="d-flex justify-content-between mb-2" style="font-size: 0.8rem; color: var(--gold); font-weight: 700;">
              <span><?php echo $tour['dest']; ?></span>
              <span><?php echo $tour['dur']; ?></span>
            </div>
            <h3 style="font-size: 1.25rem; margin-bottom: 12px;"><?php echo $tour['title']; ?></h3>
            <p style="font-size: 0.9rem; margin-bottom: 20px;">Experience the best of <?php echo $tour['dest']; ?> with our expert guides.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span style="font-size: 1.2rem; font-weight: 800;"><?php echo $tour['price']; ?></span>
              <a href="contact.php" style="font-weight: 600; font-size: 0.9rem; color: var(--green);">Book Now →</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
