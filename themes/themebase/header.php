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
	  </div>
	</header>

    <script>
    (function() {
      let lastScroll = 0;
      const header = document.querySelector('.site-header');
      
      window.addEventListener('scroll', function() {
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
    })();
    </script>

    <div class="cursor"></div>
    <div id="content" class="site-content">
