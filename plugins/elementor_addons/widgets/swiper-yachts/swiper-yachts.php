<?php
class Elementor_Widget_Swiper_Yachts extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('swiper-yachts-css', '/wp-content/plugins/elementor_addons/widgets/swiper-yachts/swiper-yachts.css');
  }

  public function get_style_depends() {
    return ['swiper-yachts-css'];
  }

  public function get_name() { 
	  return 'swiper-yachts'; 
  }

  public function get_title() {
    return __('Swiper Yachts', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-slider-push'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  /**
   * Recupera opzioni yacht in ordine alfabetico per il select.
   */
  private function get_yachts_options() {
    $options = [];

    $q = new \WP_Query([
      'post_type'      => 'yacht',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
      'fields'         => 'ids',
    ]);

    if ($q->have_posts()) {
      foreach ($q->posts as $post_id) {
        $options[$post_id] = get_the_title($post_id);
      }
    }

    wp_reset_postdata();
    return $options;
  }

  protected function register_controls() {

    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Impostazioni', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    // Nome del field ACF gallery (opzionale ma utile)
    $this->add_control(
      'gallery_field_name',
      [
        'label'       => __('Nome campo galleria ACF', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'galleria_yacht',
        'description' => __('Se esiste un campo ACF Gallery per le immagini dello yacht, inserisci qui il field name. Altrimenti useremo immagine in evidenza.', 'elementor-addon')
      ]
    );

    // Repeater per slide
    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'yacht_id',
      [
        'label'       => __('Seleziona Yacht', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'options'     => $this->get_yachts_options(),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'slides',
      [
        'label'       => __('Slide', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'title_field' => '{{{ yacht_id ? "Yacht ID: " + yacht_id : "Seleziona uno Yacht" }}}',
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Helpers di formattazione (fallback se non esistono le tue funzioni globali).
   */
  private function format_price($post_id) {
    // Se hai già creato helpers nel plugin, li usiamo
    if (function_exists('yachts_get_price_formatted')) {
      return yachts_get_price_formatted($post_id);
    }

    $price = function_exists('get_field') ? get_field('prezzo_yacht', $post_id) : null;
    if ($price === null || $price === '') return '';

    // Formatta con punto come separatore delle migliaia e senza decimali
    return number_format((float)$price, 0, ',', '.') . ' €';
  }

  private function get_spec_value($field_name, $post_id) {
    return function_exists('get_field') ? get_field($field_name, $post_id) : null;
  }

  /**
   * Recupera immagini per galleria:
   * - prima prova metabox nativa
   * - poi ACF Gallery
   * - fallback a featured image
   */
  private function get_gallery_images($post_id, $acf_gallery_field) {

    $images = [];

    // Prova prima con meta custom (metabox nativa)
    $gallery_meta = get_post_meta($post_id, $acf_gallery_field, true);
    
    if (!empty($gallery_meta)) {
      // Se è una stringa separata da virgole (metabox nativa)
      if (is_string($gallery_meta)) {
        $ids = explode(',', $gallery_meta);
        foreach ($ids as $id) {
          $id = trim($id);
          if (is_numeric($id) && $id > 0) {
            $images[] = (int) $id;
          }
        }
      }
    }

    // Fallback ACF Pro Gallery
    if (empty($images) && function_exists('get_field')) {
      $gallery = get_field($acf_gallery_field, $post_id);

      if (is_array($gallery) && !empty($gallery)) {
        foreach ($gallery as $img) {
          // ACF Gallery può restituire array o ID
          if (is_array($img) && !empty($img['ID'])) {
            $images[] = (int) $img['ID'];
          } elseif (is_numeric($img)) {
            $images[] = (int) $img;
          }
        }
      }
    }

    // Fallback featured image
    if (empty($images)) {
      $thumb_id = get_post_thumbnail_id($post_id);
      if ($thumb_id) {
        $images[] = $thumb_id;
      }
    }

    return $images;
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $slides   = $settings['slides'] ?? [];
    $gallery_field = $settings['gallery_field_name'] ?? 'galleria_yacht';

    if (empty($slides)) {
      echo '<div class="swiper-yachts-empty">Nessuna slide configurata.</div>';
      return;
    }

    $uid = $this->get_id();
    $outer_class = 'swiper-yachts-outer-' . $uid;
    ?>

    <div class="swiper-yachts-widget">

      <div class="swiper <?php echo esc_attr($outer_class); ?>">
        <div class="swiper-wrapper">

          <?php foreach ($slides as $slide) :
            $post_id = !empty($slide['yacht_id']) ? (int)$slide['yacht_id'] : 0;
            if (!$post_id) continue;

            $title     = get_the_title($post_id);
            $permalink = get_permalink($post_id);

            // DEBUG: verifica nomi campi ACF
            $cabine    = $this->get_spec_value('cabine', $post_id);
            $persone   = $this->get_spec_value('persone', $post_id);
            $lunghezza = $this->get_spec_value('lunghezza', $post_id);
            $anno      = $this->get_spec_value('anno', $post_id);
            
            // Debug temporaneo
            error_log("Yacht ID: $post_id");
            error_log("Cabine: " . var_export($cabine, true));
            error_log("Persone: " . var_export($persone, true));
            error_log("Lunghezza: " . var_export($lunghezza, true));
            error_log("Anno: " . var_export($anno, true));

            $price_formatted = $this->format_price($post_id);

            $images = $this->get_gallery_images($post_id, $gallery_field);
            $gallery_id = 'yacht-gallery-' . $uid . '-' . $post_id . '-' . wp_rand(10, 9999);
            $has_multiple_images = count($images) > 1;
          ?>

            <div class="swiper-slide">
              <article class="yacht-card">

                <div class="yacht-card__media">
                  <div id="<?php echo esc_attr($gallery_id); ?>" class="swiper swiper-yacht-gallery">
                    <div class="swiper-wrapper">
                      <?php foreach ($images as $img_id) : 
                        $img_url = wp_get_attachment_image_url($img_id, 'large');
                        $alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                      ?>
                        <div class="swiper-slide">
                          <?php if ($img_url) : ?>
                            <img
                              src="<?php echo esc_url($img_url); ?>"
                              alt="<?php echo esc_attr($alt ?: $title); ?>"
                              loading="lazy"
                            />
                          <?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    </div>

                    <?php if ($has_multiple_images) : ?>
                      <div class="swiper-button-prev yacht-gallery-prev"></div>
                      <div class="swiper-button-next yacht-gallery-next"></div>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="yacht-card__body">

                  <div class="yacht-card__specs">
                    <?php if ($cabine !== null && $cabine !== '') : ?>
                      <span class="spec spec--cabine">
                        <span class="spec__icon"><img src="/wp-content/uploads/2025/12/cabine.png" alt="" /></span>
                        <span class="spec__value"><?php echo esc_html((int)$cabine); ?></span>
                      </span>
                    <?php endif; ?>

                    <?php if ($persone !== null && $persone !== '') : ?>
                      <span class="spec spec--persone">
                        <span class="spec__icon"><img src="/wp-content/uploads/2025/12/persone.png" alt="" /></span>
                        <span class="spec__value"><?php echo esc_html((int)$persone); ?></span>
                      </span>
                    <?php endif; ?>

                    <?php if ($lunghezza !== null && $lunghezza !== '') : ?>
                      <span class="spec spec--lunghezza">
                        <span class="spec__icon"><img src="/wp-content/uploads/2025/12/lunghezza.png" alt="" /></span>
                        <span class="spec__value"><?php echo esc_html( number_format_i18n((float)$lunghezza, 0) ); ?> m</span>
                      </span>
                    <?php endif; ?>

                    <?php if ($anno !== null && $anno !== '') : ?>
                      <span class="spec spec--anno">
                        <span class="spec__icon"><img src="/wp-content/uploads/2025/12/anno.png" alt="" /></span>
                        <span class="spec__value"><?php echo esc_html((int)$anno); ?></span>
                      </span>
                    <?php endif; ?>
                  </div>

                  <h5 class="yacht-card__title">
                    <?php echo esc_html($title); ?>
                  </h5>

                  <?php if ($price_formatted) : ?>
                    <div class="yacht-card__price">
                      <h6><?php echo $price_formatted; ?></h6>
                    </div>
                  <?php endif; ?>

                  <div class="yacht-card__cta">
                    <a class="hov-btn learn-more" href="<?php echo esc_url($permalink); ?>">
                      <span class="circle" aria-hidden="true">
                        <span class="icon arrow"></span>
                      </span>
                      <span class="button-text">SCOPRI DI PIÙ</span>
                    </a>
                  </div>

                </div>
              </article>
            </div>

          <?php endforeach; ?>

        </div>

        <!-- Scrollbar e Navigation sulla stessa riga -->
        <div class="swiper-controls">
          <div class="swiper-scrollbar yachts-scrollbar"></div>
          <div class="swiper-navigation">
            <div class="swiper-button-prev yachts-outer-prev"></div>
            <div class="swiper-button-next yachts-outer-next"></div>
          </div>
        </div>
      </div>

    </div>

       <script>
(function () {

  function initYachtsSwiper_<?php echo esc_js($uid); ?>() {
    // Se Swiper non è ancora disponibile, esci senza errori
    if (typeof Swiper === 'undefined') return;

    var outerEl = document.querySelector('.<?php echo esc_js($outer_class); ?>');
    if (!outerEl) return;

    // Evita doppie inizializzazioni
    if (outerEl.dataset.swiperInit === '1') return;
    outerEl.dataset.swiperInit = '1';

    // Inner galleries per card (inizializziamo prima gli swiper interni)
    var galleries = outerEl.querySelectorAll('.swiper-yacht-gallery');
    galleries.forEach(function (galleryEl) {

      if (galleryEl.dataset.swiperInit === '1') return;
      galleryEl.dataset.swiperInit = '1';

      var next = galleryEl.querySelector('.yacht-gallery-next');
      var prev = galleryEl.querySelector('.yacht-gallery-prev');

      new Swiper(galleryEl, {
        slidesPerView: 1,
        loop: false,
        speed: 450,
        nested: true,
        navigation: { nextEl: next, prevEl: prev }
      });
    });

    // Outer swiper (inizializziamo dopo gli inner)
    new Swiper(outerEl, {
      slidesPerView: 3,
      spaceBetween: 28,
      speed: 500,
      touchRatio: 0.2,
      resistanceRatio: 0.85,
      navigation: {
        nextEl: '.yachts-outer-next',
        prevEl: '.yachts-outer-prev'
      },
      scrollbar: {
        el: '.yachts-scrollbar',
        draggable: true,
        dragSize: 'auto'
      },
      breakpoints: {
        0: { slidesPerView: 1.1, spaceBetween: 16 },
        768: { slidesPerView: 2, spaceBetween: 22 },
        1024: { slidesPerView: 3, spaceBetween: 28 }
      }
    });
  }

  // Elementor può renderizzare dopo DOMContentLoaded:
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initYachtsSwiper_<?php echo esc_js($uid); ?>);
  } else {
    initYachtsSwiper_<?php echo esc_js($uid); ?>();
  }

  // Hook Elementor frontend (senza registrare script nel constructor)
  if (window.elementorFrontend && elementorFrontend.hooks) {
    elementorFrontend.hooks.addAction('frontend/element_ready/swiper-yachts.default', function () {
      initYachtsSwiper_<?php echo esc_js($uid); ?>();
    });
  }

})();
</script>


<?php
  }	  

  protected function content_template() {}
}