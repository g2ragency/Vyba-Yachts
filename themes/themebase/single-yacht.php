<?php
/**
 * Template per la singola pagina yacht
 */

get_header();
?>

<div class="single-yacht-container">
  <?php while (have_posts()) : the_post();
    
    // Recupera i dati ACF
    $gallery_meta = get_post_meta(get_the_ID(), 'galleria_yacht', true);
    $gallery_ids = !empty($gallery_meta) ? explode(',', $gallery_meta) : [];
    
    // ACF fields
    $cabine = get_field('cabine');
    $persone = get_field('persone');
    $lunghezza = get_field('lunghezza');
    $anno = get_field('anno');
    // Price: try multiple possible fields/meta keys
    $prezzo = get_field('prezzo');
    if (empty($prezzo)) {
      $prezzo = get_field('prezzo_yacht');
    }
    if (empty($prezzo)) {
      $meta_prezzo = get_post_meta(get_the_ID(), 'prezzo_yacht', true);
      if (!empty($meta_prezzo)) {
        $prezzo = $meta_prezzo;
      }
    }
    $link_brochure = get_field('link_brochure');
  ?>

    <!-- Breadcrumb -->
    <nav class="yacht-breadcrumb" aria-label="Breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Homepage</a>
      <span class="sep">/</span>
      <a href="<?php echo esc_url(home_url('/vendita/')); ?>">Vendita</a>
      <span class="sep">/</span>
      <span class="current"><?php the_title(); ?></span>
    </nav>

    <div class="yacht-layout">
      
      <!-- Contenuto principale a sinistra -->
      <main class="yacht-main-content">

        <!-- Galleria immagini -->
        <?php if (!empty($gallery_ids)) : ?>
          <div class="yacht-gallery-section">
            <div class="yacht-main-image">
              <?php 
              $main_image = wp_get_attachment_image_url($gallery_ids[0], 'large');
              if ($main_image) :
              ?>
                <img src="<?php echo esc_url($main_image); ?>" alt="<?php the_title(); ?>" />
              <?php endif; ?>
            </div>

            <?php if (count($gallery_ids) > 1) : ?>
              <div class="yacht-thumbnails">
                <?php foreach (array_slice($gallery_ids, 0, 4) as $img_id) : 
                  $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                  if ($thumb_url) :
                ?>
                  <div class="yacht-thumb">
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="" />
                  </div>
                <?php 
                  endif;
                endforeach; 
                ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Descrizione -->
        <div class="yacht-description-section">
          <h2 class="section-title">Descrizione imbarcazione</h2>
          <div class="yacht-description-content">
            <?php the_content(); ?>
          </div>
        </div>

        <!-- Placeholder per altri contenuti -->
        <div class="yacht-related-section">
          <h2 class="section-title">Potrebbero interessarti anche questi modelli di yachts</h2>
          <!-- Qui potresti aggiungere yachts correlati -->
        </div>

        <!-- CTA finale -->
        <div class="yacht-cta-section">
          <h2>Richiedi informazioni su questa imbarcazione e mettiti in contatto con il venditore</h2>
          <button class="yacht-contact-final-btn">RICHIEDI INFORMAZIONI</button>
        </div>

      </main>

      <!-- Sidebar fissa a destra -->
      <aside class="yacht-sidebar">
        <div class="yacht-sidebar-inner">
          
          <h1 class="yacht-title"><?php the_title(); ?></h1>
          
          <div class="yacht-price">
            <?php if (isset($prezzo) && $prezzo !== '' && $prezzo !== null) : 
              // Normalize number: remove thousand separators and convert comma decimals to dot
              $prezzo_raw = $prezzo;
              $num = str_replace('.', '', $prezzo_raw);
              $num = str_replace(',', '.', $num);
              $value = floatval($num);
              $formatted = number_format($value, 0, ',', '.');
            ?>
              <?php echo esc_html($formatted); ?> €
            <?php else : ?>
              Prezzo su richiesta
            <?php endif; ?>
          </div>

          <div class="yacht-specs-list">
            <?php if ($cabine) : ?>
              <div class="yacht-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/cabine.png" alt="Cabine" /></span>
                <span class="spec-value"><?php echo esc_html((int)$cabine); ?></span>
              </div>
            <?php endif; ?>

            <?php if ($persone) : ?>
              <div class="yacht-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/persone.png" alt="Persone" /></span>
                <span class="spec-value"><?php echo esc_html((int)$persone); ?></span>
              </div>
            <?php endif; ?>

            <?php if ($lunghezza) : ?>
              <div class="yacht-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/lunghezza.png" alt="Lunghezza" /></span>
                <span class="spec-value"><?php echo esc_html(number_format_i18n((float)$lunghezza, 0)); ?> m</span>
              </div>
            <?php endif; ?>

            <?php if ($anno) : ?>
              <div class="yacht-spec-item">
                <span class="spec-icon"><img src="/wp-content/uploads/2025/12/anno.png" alt="Anno" /></span>
                <span class="spec-value"><?php echo esc_html((int)$anno); ?></span>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($link_brochure) : ?>
            <a href="<?php echo esc_url($link_brochure); ?>" class="yacht-brochure-btn" target="_blank">
              VEDI BROCHURE
            </a>
          <?php endif; ?>

          <button class="yacht-contact-btn">CONTATTACI</button>

        </div>
      </aside>

    </div>

  <?php endwhile; ?>
</div>

<?php get_footer(); ?>
