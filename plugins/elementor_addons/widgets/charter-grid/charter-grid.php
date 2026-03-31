<?php
class Elementor_Widget_Charter_Grid extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('charter-grid-css', '/wp-content/plugins/elementor_addons/widgets/charter-grid/charter-grid.css');
  }

  public function get_style_depends() {
    return ['charter-grid-css'];
  }

  public function get_name() { 
    return 'charter-grid'; 
  }

  public function get_title() {
    return __('Charter Grid', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-gallery-grid'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  /**
   * Recupera opzioni charter in ordine alfabetico per il select.
   */
  private function get_charter_options() {
    $options = [];

    $q = new \WP_Query([
      'post_type'      => 'charter',
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

    $this->add_control(
      'exclude_charter',
      [
        'label'       => __('Escludi Charter', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'multiple'    => true,
        'options'     => $this->get_charter_options(),
        'label_block' => true,
        'description' => __('Seleziona i charter da escludere dalla griglia', 'elementor-addon'),
      ]
    );

    $this->add_control(
      'gallery_field_name',
      [
        'label'       => __('Nome campo galleria ACF', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'galleria_charter',
        'description' => __('Nome del campo ACF Gallery per le immagini del charter', 'elementor-addon')
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Helpers di formattazione
   */
  private function format_price($post_id) {
    if (function_exists('charter_get_price_formatted')) {
      return charter_get_price_formatted($post_id);
    }

    $price = function_exists('get_field') ? get_field('prezzo_charter', $post_id) : null;
    if ($price === null || $price === '') return '';

    return number_format((float)$price, 0, ',', '.') . ' €';
  }

  private function get_spec_value($field_name, $post_id) {
    return function_exists('get_field') ? get_field($field_name, $post_id) : null;
  }

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
    $exclude_ids = !empty($settings['exclude_charter']) ? $settings['exclude_charter'] : [];
    $gallery_field = $settings['gallery_field_name'] ?? 'galleria_charter';

    // Query tutti i charter tranne quelli esclusi
    $args = [
      'post_type'      => 'charter',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    ];

    if (!empty($exclude_ids)) {
      $args['post__not_in'] = $exclude_ids;
    }

    $query = new \WP_Query($args);

    if (!$query->have_posts()) {
      echo '<p>Nessun charter disponibile.</p>';
      return;
    }

    $uid = $this->get_id();
    ?>

    <div class="charter-grid-widget">
      <div class="charter-grid">

        <?php while ($query->have_posts()) : $query->the_post();
          $post_id = get_the_ID();
          $title     = get_the_title();
          $permalink = get_permalink();

          $cabine    = $this->get_spec_value('cabine', $post_id);
          $persone   = $this->get_spec_value('persone', $post_id);
          $lunghezza = $this->get_spec_value('lunghezza', $post_id);
          $anno      = $this->get_spec_value('anno', $post_id);

          $price_formatted = $this->format_price($post_id);
          $images = $this->get_gallery_images($post_id, $gallery_field);
          $gallery_id = 'charter-grid-gallery-' . $uid . '-' . $post_id . '-' . wp_rand(10, 9999);
          $has_multiple_images = count($images) > 1;
        ?>

          <article class="charter-grid-card">

            <div class="charter-grid-card__media">
              <div id="<?php echo esc_attr($gallery_id); ?>" class="swiper swiper-charter-grid-gallery">
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
                  <div class="swiper-button-prev charter-grid-gallery-prev"></div>
                  <div class="swiper-button-next charter-grid-gallery-next"></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="charter-grid-card__body">

              <div class="charter-grid-card__specs">
                <?php if ($cabine !== null && $cabine !== '') : ?>
                  <span class="grid-spec grid-spec--cabine">
                    <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/cabine.png" alt="" /></span>
                    <span class="grid-spec__value"><?php echo esc_html((int)$cabine); ?></span>
                  </span>
                <?php endif; ?>

                <?php if ($persone !== null && $persone !== '') : ?>
                  <span class="grid-spec grid-spec--persone">
                    <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/persone.png" alt="" /></span>
                    <span class="grid-spec__value"><?php echo esc_html((int)$persone); ?></span>
                  </span>
                <?php endif; ?>

                <?php if ($lunghezza !== null && $lunghezza !== '') : ?>
                  <span class="grid-spec grid-spec--lunghezza">
                    <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/lunghezza.png" alt="" /></span>
                    <span class="grid-spec__value"><?php echo esc_html( number_format_i18n((float)$lunghezza, 0) ); ?> m</span>
                  </span>
                <?php endif; ?>

                <?php if ($anno !== null && $anno !== '') : ?>
                  <span class="grid-spec grid-spec--anno">
                    <span class="grid-spec__icon"><img src="/wp-content/uploads/2025/12/anno.png" alt="" /></span>
                    <span class="grid-spec__value"><?php echo esc_html((int)$anno); ?></span>
                  </span>
                <?php endif; ?>
              </div>

              <h5 class="charter-grid-card__title">
                <?php echo esc_html($title); ?>
              </h5>

              <?php if ($price_formatted) : ?>
                <div class="charter-grid-card__price">
                  <h6><?php echo $price_formatted; ?></h6>
                </div>
              <?php endif; ?>

              <div class="charter-grid-card__cta">
                <a class="hov-btn learn-more" href="<?php echo esc_url($permalink); ?>">
                  <span class="circle" aria-hidden="true">
                    <span class="icon arrow"></span>
                  </span>
                  <span class="button-text">SCOPRI DI PIÙ</span>
                </a>
              </div>

            </div>
          </article>

        <?php endwhile; wp_reset_postdata(); ?>

      </div>
    </div>

    <script>
    (function () {
      function initCharterGridSwiper_<?php echo esc_js($uid); ?>() {
        if (typeof Swiper === 'undefined') return;

        var galleries = document.querySelectorAll('.charter-grid-widget .swiper-charter-grid-gallery');
        
        galleries.forEach(function(gallery) {
          if (gallery.dataset.swiperInit === '1') return;
          gallery.dataset.swiperInit = '1';

          var galleryId = gallery.id;
          
          new Swiper('#' + galleryId, {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: false,
            navigation: {
              nextEl: '#' + galleryId + ' .charter-grid-gallery-next',
              prevEl: '#' + galleryId + ' .charter-grid-gallery-prev'
            }
          });
        });
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharterGridSwiper_<?php echo esc_js($uid); ?>);
      } else {
        initCharterGridSwiper_<?php echo esc_js($uid); ?>();
      }

      window.addEventListener('load', initCharterGridSwiper_<?php echo esc_js($uid); ?>);
    })();
    </script>
    <?php
  }

  protected function content_template() {
    ?>
    <div class="charter-grid-widget">
      <p>Anteprima non disponibile. Salva e visualizza in frontend.</p>
    </div>
    <?php
  }
}
