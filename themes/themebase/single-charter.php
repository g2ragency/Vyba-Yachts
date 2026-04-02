<?php
/**
 * Template per la singola pagina charter
 */

get_header();
?>

<div class="single-charter-container">
  <?php while (have_posts()) : the_post();
    
    // Recupera i dati galleria
    $gallery_meta = get_post_meta(get_the_ID(), 'galleria_charter', true);
    $gallery_ids = !empty($gallery_meta) ? explode(',', $gallery_meta) : [];
    
    // ACF fields
    $cabine = get_field('cabine');
    $persone = get_field('persone');
    $lunghezza = get_field('lunghezza');
    $anno = get_field('anno');
    // Price: try multiple possible fields/meta keys
    $prezzo = get_field('prezzo');
    if (empty($prezzo)) {
      $prezzo = get_field('prezzo_charter');
    }
    if (empty($prezzo)) {
      $meta_prezzo = get_post_meta(get_the_ID(), 'prezzo_charter', true);
      if (!empty($meta_prezzo)) {
        $prezzo = $meta_prezzo;
      }
    }
    $link_brochure = get_field('link_brochure');
    
    // Scheda Tecnica PDF
    $scheda_tecnica_id = get_post_meta(get_the_ID(), 'scheda_tecnica_charter', true);
    $scheda_tecnica_url = $scheda_tecnica_id ? wp_get_attachment_url($scheda_tecnica_id) : '';
  ?>

    <!-- Breadcrumb -->
    <nav class="charter-breadcrumb" aria-label="Breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Homepage</a>
      <span class="sep">/</span>
      <a href="<?php echo esc_url(home_url('/charter/')); ?>">Charter</a>
      <span class="sep">/</span>
      <span class="current"><?php the_title(); ?></span>
    </nav>

    <div class="charter-layout">
      
      <!-- Contenuto principale a sinistra -->
      <main class="charter-main-content">

        <!-- Galleria immagini -->
        <?php if (!empty($gallery_ids)) : ?>
          <div class="charter-gallery-section" data-gallery-count="<?php echo count($gallery_ids); ?>">
            
            <!-- Main image container with navigation arrows -->
            <div class="charter-main-image-wrapper">
              <div class="charter-main-image" id="charter-main-image">
                <?php 
                $main_image = wp_get_attachment_image_url($gallery_ids[0], 'large');
                $main_full = wp_get_attachment_image_url($gallery_ids[0], 'full');
                if ($main_image) :
                ?>
                  <img 
                    src="<?php echo esc_url($main_image); ?>" 
                    alt="<?php the_title(); ?>"
                    data-full="<?php echo esc_url($main_full); ?>"
                    class="charter-main-img"
                  />
                <?php endif; ?>
              </div>

              <?php if (count($gallery_ids) > 1) : ?>
                <button class="charter-nav-arrow charter-nav-prev" aria-label="Immagine precedente">
                  <svg width="11" height="22" viewBox="0 0 11 22" fill="none">
                    <path d="M10 1L1 11L10 21" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </button>
                <button class="charter-nav-arrow charter-nav-next" aria-label="Immagine successiva">
                  <svg width="11" height="22" viewBox="0 0 11 22" fill="none">
                    <path d="M1 1L10 11L1 21" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </button>
              <?php endif; ?>
            </div>

            <!-- Thumbnails scrollable -->
            <?php if (count($gallery_ids) > 1) : ?>
              <div class="charter-thumbnails-container">
                <div class="charter-thumbnails" id="charter-thumbnails">
                  <?php foreach ($gallery_ids as $index => $img_id) : 
                    $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                    $full_url = wp_get_attachment_image_url($img_id, 'full');
                    $large_url = wp_get_attachment_image_url($img_id, 'large');
                    if ($thumb_url) :
                  ?>
                    <div class="charter-thumb <?php echo $index === 0 ? 'active' : ''; ?>" 
                         data-index="<?php echo $index; ?>"
                         data-large="<?php echo esc_url($large_url); ?>"
                         data-full="<?php echo esc_url($full_url); ?>">
                      <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?> - Immagine <?php echo $index + 1; ?>" />
                    </div>
                  <?php 
                    endif;
                  endforeach; 
                  ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Descrizione -->
        <div class="charter-description-section">
          <h6>INFORMAZIONI</h6>
          <h3 class="section-title">Descrizione imbarcazione</h3>
          <div class="charter-description-content">
            <?php the_content(); ?>
          </div>
        </div>

        <!-- Charter correlati -->
        <div class="charter-related-section">
          <h6>CHARTER</h6>
          <h3 class="section-title">Potrebbero interessarti anche questi charter</h3>
          
          <?php
          // Query per gli ultimi charter escluso quello corrente
          $related_args = [
            'post_type'      => 'charter',
            'post_status'    => 'publish',
            'posts_per_page' => 6,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => [get_the_ID()],
          ];
          
          $related_query = new WP_Query($related_args);
          
          if ($related_query->have_posts()) :
          ?>
            <div class="charter-related-carousel">
              <div class="swiper charter-related-swiper">
                <div class="swiper-wrapper">
                  
                  <?php while ($related_query->have_posts()) : $related_query->the_post();
                    $charter_id = get_the_ID();
                    $charter_title = get_the_title();
                    $charter_link = get_permalink();
                    
                    // ACF fields
                    $charter_cabine = get_field('cabine', $charter_id);
                    $charter_persone = get_field('persone', $charter_id);
                    $charter_lunghezza = get_field('lunghezza', $charter_id);
                    $charter_anno = get_field('anno', $charter_id);
                    
                    // Prezzo
                    $charter_prezzo = get_field('prezzo', $charter_id);
                    if (empty($charter_prezzo)) {
                      $charter_prezzo = get_field('prezzo_charter', $charter_id);
                    }
                    $charter_price_formatted = '';
                    if ($charter_prezzo !== null && $charter_prezzo !== '') {
                      $num = str_replace('.', '', $charter_prezzo);
                      $num = str_replace(',', '.', $num);
                      $value = floatval($num);
                      $charter_price_formatted = number_format($value, 0, ',', '.') . ' € / al giorno';
                    }
                    
                    // Galleria
                    $charter_gallery_meta = get_post_meta($charter_id, 'galleria_charter', true);
                    $charter_gallery_ids = !empty($charter_gallery_meta) ? explode(',', $charter_gallery_meta) : [];
                    $charter_gallery_id = 'charter-related-gallery-' . $charter_id . '-' . wp_rand(10, 9999);
                    $charter_has_multiple = count($charter_gallery_ids) > 1;
                  ?>
                  
                    <div class="swiper-slide">
                      <article class="charter-grid-card">
                        
                        <div class="charter-grid-card__media">
                          <div id="<?php echo esc_attr($charter_gallery_id); ?>" class="swiper swiper-charter-grid-gallery">
                            <div class="swiper-wrapper">
                              <?php foreach ($charter_gallery_ids as $img_id) : 
                                $img_url = wp_get_attachment_image_url($img_id, 'large');
                                $alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                              ?>
                                <div class="swiper-slide">
                                  <?php if ($img_url) : ?>
                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($alt ?: $charter_title); ?>" loading="lazy" />
                                  <?php endif; ?>
                                </div>
                              <?php endforeach; ?>
                            </div>
                            
                            <?php if ($charter_has_multiple) : ?>
                              <div class="swiper-button-prev charter-grid-gallery-prev"></div>
                              <div class="swiper-button-next charter-grid-gallery-next"></div>
                            <?php endif; ?>
                          </div>
                        </div>
                        
                        <div class="charter-grid-card__body">
                          
                          <div class="charter-grid-card__specs">
                            <?php if ($charter_cabine) : ?>
                              <span class="grid-spec grid-spec--cabine">
                                <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/cabine.png" alt="" /></span>
                                <span class="grid-spec__value"><?php echo esc_html((int)$charter_cabine); ?></span>
                              </span>
                            <?php endif; ?>
                            
                            <?php if ($charter_persone) : ?>
                              <span class="grid-spec grid-spec--persone">
                                <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/persone.png" alt="" /></span>
                                <span class="grid-spec__value"><?php echo esc_html((int)$charter_persone); ?></span>
                              </span>
                            <?php endif; ?>
                            
                            <?php if ($charter_lunghezza) : ?>
                              <span class="grid-spec grid-spec--lunghezza">
                                <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/lunghezza.png" alt="" /></span>
                                <span class="grid-spec__value"><?php echo esc_html(number_format_i18n((float)$charter_lunghezza, 0)); ?> m</span>
                              </span>
                            <?php endif; ?>
                            
                            <?php if ($charter_anno) : ?>
                              <span class="grid-spec grid-spec--anno">
                                <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/anno.png" alt="" /></span>
                                <span class="grid-spec__value"><?php echo esc_html((int)$charter_anno); ?></span>
                              </span>
                            <?php endif; ?>
                          </div>
                          
                          <h5 class="charter-grid-card__title"><?php echo esc_html($charter_title); ?></h5>
                          
                          <?php if ($charter_price_formatted) : ?>
                            <div class="charter-grid-card__price">
                              <h6><?php echo $charter_price_formatted; ?></h6>
                            </div>
                          <?php endif; ?>
                          
                          <div class="charter-grid-card__cta">
                            <a class="hov-btn learn-more" href="<?php echo esc_url($charter_link); ?>">
                              <span class="circle" aria-hidden="true">
                                <span class="icon arrow"></span>
                              </span>
                              <span class="button-text">SCOPRI DI PIÙ</span>
                            </a>
                          </div>
                          
                        </div>
                      </article>
                    </div>
                  
                  <?php endwhile; wp_reset_postdata(); ?>
                  
                </div>
              </div>
              
              <!-- Scrollbar and Navigation -->
              <div class="charter-related-scrollbar">
                <div class="swiper-scrollbar"></div>
                <div class="charter-related-navigation">
                  <div class="swiper-button-prev charter-related-prev"></div>
                  <div class="swiper-button-next charter-related-next"></div>
                </div>
              </div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
              if (typeof Swiper === 'undefined') return;
              
              // Inizializza gallerie interne delle card
              var innerGalleries = document.querySelectorAll('.charter-related-carousel .swiper-charter-grid-gallery');
              innerGalleries.forEach(function(gallery) {
                if (gallery.dataset.swiperInit === '1') return;
                gallery.dataset.swiperInit = '1';
                
                new Swiper('#' + gallery.id, {
                  slidesPerView: 1,
                  spaceBetween: 0,
                  loop: false,
                  navigation: {
                    nextEl: '#' + gallery.id + ' .charter-grid-gallery-next',
                    prevEl: '#' + gallery.id + ' .charter-grid-gallery-prev'
                  }
                });
              });
              
              // Inizializza carousel principale
              new Swiper('.charter-related-swiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: {
                  nextEl: '.charter-related-next',
                  prevEl: '.charter-related-prev',
                },
                scrollbar: {
                  el: '.charter-related-scrollbar .swiper-scrollbar',
                  draggable: true,
                },
                breakpoints: {
                  768: {
                    slidesPerView: 2,
                    spaceBetween: 30,
                  },
                  1024: {
                    slidesPerView: 2,
                    spaceBetween: 40,
                  }
                }
              });
            });
            </script>
          <?php endif; ?>
        </div>

        <!-- CTA finale -->
        <div class="charter-cta-section" id="charter-contact-section">
          <h6>CONTATTI</h6>
          <h3>Richiedi informazioni su questa imbarcazione e mettiti in contatto con noi</h3>
          <p class="p-cta">Compila il form per ricevere scheda dettagliata, ulteriori foto, video e una consulenza dedicata. Ti ricontattiamo in breve per valutare insieme se questo è davvero il charter giusto per te.</p>
          <?php echo do_shortcode('[contact-form-7 id="fbe7748" title="Modulo di contatto"]'); ?>
        </div>

      </main>

      <!-- Sidebar fissa a destra -->
      <aside class="charter-sidebar">
        <div class="charter-sidebar-inner">
          
          <h1 class="charter-title"><?php the_title(); ?></h1>

          <div class="charter-specs-list">
            <?php if ($cabine) : ?>
              <div class="charter-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/cabine.png" alt="Cabine" /></span>
                <span class="spec-value"><?php echo esc_html((int)$cabine); ?></span>
              </div>
            <?php endif; ?>

            <?php if ($persone) : ?>
              <div class="charter-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/persone.png" alt="Persone" /></span>
                <span class="spec-value"><?php echo esc_html((int)$persone); ?></span>
              </div>
            <?php endif; ?>

            <?php if ($lunghezza) : ?>
              <div class="charter-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/lunghezza.png" alt="Lunghezza" /></span>
                <span class="spec-value"><?php echo esc_html(number_format_i18n((float)$lunghezza, 0)); ?> m</span>
              </div>
            <?php endif; ?>

            <?php if ($anno) : ?>
              <div class="charter-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/anno.png" alt="Anno" /></span>
                <span class="spec-value"><?php echo esc_html((int)$anno); ?></span>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="charter-price">
            <?php if (isset($prezzo) && $prezzo !== '' && $prezzo !== null) : 
              // Normalize number: remove thousand separators and convert comma decimals to dot
              $prezzo_raw = $prezzo;
              $num = str_replace('.', '', $prezzo_raw);
              $num = str_replace(',', '.', $num);
              $value = floatval($num);
              $formatted = number_format($value, 0, ',', '.');
            ?>
              <?php echo esc_html($formatted); ?> € / al giorno
            <?php else : ?>
              Prezzo su richiesta
            <?php endif; ?>
          </div>

          <?php if ($scheda_tecnica_url) : ?>
            <a href="<?php echo esc_url($scheda_tecnica_url); ?>" class="charter-scheda-tecnica-btn" target="_blank" download>
              <span class="btn-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/download-icon.png" alt="Download" width="50" height="50">
              </span>
              <span class="btn-text">SCARICA LA SCHEDA TECNICA</span>
            </a>
          <?php endif; ?>

          <?php if ($link_brochure) : ?>
            <a href="<?php echo esc_url($link_brochure); ?>" class="charter-brochure-btn" target="_blank">
              VEDI BROCHURE
            </a>
          <?php endif; ?>

          <a href="#charter-contact-section" class="charter-contact-btn">CONTATTACI</a>

        </div>
      </aside>

    </div>

  <?php endwhile; ?>
</div>

<?php get_footer(); ?>
