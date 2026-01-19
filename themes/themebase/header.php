<?php
/**
 * The header for our theme
 *
 * @package WP_Bootstrap_Starter
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <!-- SWIPER -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- LENIS -->
  <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis"></script>

  <!-- GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

  <!-- SplitType -->
  <script src="https://unpkg.com/split-type"></script>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <?php
  if (function_exists('wp_body_open')) {
    wp_body_open();
  } else {
    do_action('wp_body_open');
  }
  ?>

  <div id="page" class="site">
    <header class="site-header">
	  <div class="header-container">
		<!-- Menu a sinistra -->
		<div class="nav-left">
		  <?php
			wp_nav_menu(array(
			  'theme_location' => 'primary-left',
			  'menu_class'     => 'main-menu',
			  'container'      => false,
			));
		  ?>
		</div>

		<!-- Logo al centro -->
		<div class="logo-container">
		  <img src="/wp-content/uploads/2025/12/logo.png" alt="Logo" class="logo">
		</div>

		<!-- Menu a destra -->
		<div class="nav-right">
		  <?php
			wp_nav_menu(array(
			  'theme_location' => 'primary-right',
			  'menu_class'     => 'main-menu',
			  'container'      => false,
			));
		  ?>
		</div>

		<!-- Mobile Menu Toggle -->
		<button class="mobile-menu-toggle" aria-label="Menu">
		  <span></span>
		  <span></span>
		  <span></span>
		</button>
	  </div>

	  <!-- Mobile Menu Overlay -->
	  <div class="mobile-menu-overlay">
		<div class="mobile-menu-header">
		  <img src="/wp-content/uploads/2025/12/logo.png" alt="Logo" class="mobile-menu-logo">
		  <button class="mobile-menu-close" aria-label="Close">
			<span></span>
			<span></span>
		  </button>
		</div>
		<nav class="mobile-menu-nav">
		  <?php
			$left_menu = wp_get_nav_menu_items(get_nav_menu_locations()['primary-left']);
			$right_menu = wp_get_nav_menu_items(get_nav_menu_locations()['primary-right']);
			$all_menu_items = array_merge($left_menu ? $left_menu : [], $right_menu ? $right_menu : []);
			
			if ($all_menu_items) {
			  echo '<ul class="mobile-menu-list">';
			  foreach ($all_menu_items as $item) {
				echo '<li><a href="' . $item->url . '">' . $item->title . '</a></li>';
			  }
			  echo '</ul>';
			}
		  ?>
		</nav>
		<div class="mobile-menu-footer">
		  <p>Box 27 – Marina di Cala Galera<br>58019 Monte Argentario</p>
		  <a href="mailto:info@vybayachts.com">info@vybayachts.com</a>
		  <div class="mobile-menu-lang">
			<a href="#" class="active">IT</a>
			<a href="#">EN</a>
		  </div>
		</div>
	  </div>
	</header>

    <script>
    (function() {
      let lastScroll = 0;
      const header = document.querySelector('.site-header');
      let mobileMenuOpen = false;
      
      window.addEventListener('scroll', function() {
        // Don't hide header on mobile when menu is open
        if (window.innerWidth <= 768 && mobileMenuOpen) {
          return;
        }

        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
          // At top: remove all classes
          header.classList.remove('header-hidden', 'header-scrolled');
        } else if (currentScroll > lastScroll) {
          // Scrolling down: hide header
          header.classList.add('header-hidden');
          header.classList.remove('header-scrolled');
        } else {
          // Scrolling up: show header with blue background
          header.classList.remove('header-hidden');
          header.classList.add('header-scrolled');
        }
        
        lastScroll = currentScroll;
      });

      // Mobile menu toggle
      const menuToggle = document.querySelector('.mobile-menu-toggle');
      const menuClose = document.querySelector('.mobile-menu-close');
      const menuOverlay = document.querySelector('.mobile-menu-overlay');
      const body = document.body;

      if (menuToggle && menuOverlay) {
        menuToggle.addEventListener('click', function() {
          menuOverlay.classList.add('active');
          body.style.overflow = 'hidden';
          mobileMenuOpen = true;
        });

        menuClose.addEventListener('click', function() {
          menuOverlay.classList.remove('active');
          body.style.overflow = '';
          mobileMenuOpen = false;
        });

        // Close on link click
        const menuLinks = menuOverlay.querySelectorAll('a');
        menuLinks.forEach(link => {
          link.addEventListener('click', function() {
            menuOverlay.classList.remove('active');
            body.style.overflow = '';
            mobileMenuOpen = false;
          });
        });
      }
    })();
    </script>

    <div class="cursor"></div>
    <div id="content" class="site-content">
