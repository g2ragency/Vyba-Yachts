<?php
class Elementor_Widget_Yachts_Grid extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('yachts-grid-css', '/wp-content/plugins/elementor_addons/widgets/yachts-grid/yachts-grid.css');
  }

  public function get_style_depends() {
    return ['yachts-grid-css'];
  }

  public function get_name() { 
    return 'yachts-grid'; 
  }

  public function get_title() {
    return __('Yachts Grid', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-gallery-grid'; 
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

    $this->add_control(
      'exclude_yachts',
      [
        'label'       => __('Escludi Yacht', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'multiple'    => true,
        'options'     => $this->get_yachts_options(),
        'label_block' => true,
        'description' => __('Seleziona gli yacht da escludere dalla griglia', 'elementor-addon'),
      ]
    );

    $this->add_control(
      'gallery_field_name',
      [
        'label'       => __('Nome campo galleria ACF', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'galleria_yacht',
        'description' => __('Nome del campo ACF Gallery per le immagini dello yacht', 'elementor-addon')
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Helpers di formattazione
   */
  private function format_price($post_id) {
    if (function_exists('yachts_get_price_formatted')) {
      return yachts_get_price_formatted($post_id);
    }

    $price = function_exists('get_field') ? get_field('prezzo_yacht', $post_id) : null;
    if ($price === null || $price === '') return '';

    return number_format((float)$price, 0, ',', '.') . ' €';
  }

  private function get_spec_value($field_name, $post_id) {
    return function_exists('get_field') ? get_field($field_name, $post_id) : null;
  }

  private function get_gallery_images($post_id, $acf_gallery_field) {
    $images = [];

    if (function_exists('get_field') && $acf_gallery_field) {
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
    $exclude_ids = !empty($settings['exclude_yachts']) ? $settings['exclude_yachts'] : [];
    $gallery_field = $settings['gallery_field_name'] ?? 'galleria_yacht';

    // Query tutti gli yacht tranne quelli esclusi
    $args = [
      'post_type'      => 'yacht',
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
      echo '<p>Nessuno yacht disponibile.</p>';
      return;
    }

    $uid = $this->get_id();
    ?>

    <div class="yachts-grid-widget">
      <div class="yachts-grid">

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
          $gallery_id = 'yacht-grid-gallery-' . $uid . '-' . $post_id . '-' . wp_rand(10, 9999);
        ?>

          <article class="yacht-grid-card">

            <div class="yacht-grid-card__media">
              <div id="<?php echo esc_attr($gallery_id); ?>" class="swiper swiper-yacht-grid-gallery">
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

                <div class="swiper-button-prev yacht-grid-gallery-prev"></div>
                <div class="swiper-button-next yacht-grid-gallery-next"></div>
              </div>
            </div>

            <div class="yacht-grid-card__body">

              <div class="yacht-grid-card__specs">
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

              <h5 class="yacht-grid-card__title">
                <?php echo esc_html($title); ?>
              </h5>

              <?php if ($price_formatted) : ?>
                <div class="yacht-grid-card__price">
                  <h6><?php echo $price_formatted; ?></h6>
                </div>
              <?php endif; ?>

              <div class="yacht-grid-card__cta">
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
      function initYachtsGridSwiper_<?php echo esc_js($uid); ?>() {
        if (typeof Swiper === 'undefined') return;

        var galleries = document.querySelectorAll('.yachts-grid-widget .swiper-yacht-grid-gallery');
        
        galleries.forEach(function(gallery) {
          if (gallery.dataset.swiperInit === '1') return;
          gallery.dataset.swiperInit = '1';

          var galleryId = gallery.id;
          
          new Swiper('#' + galleryId, {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: false,
            navigation: {
              nextEl: '#' + galleryId + ' .yacht-grid-gallery-next',
              prevEl: '#' + galleryId + ' .yacht-grid-gallery-prev'
            }
          });
        });
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initYachtsGridSwiper_<?php echo esc_js($uid); ?>);
      } else {
        initYachtsGridSwiper_<?php echo esc_js($uid); ?>();
      }

      window.addEventListener('load', initYachtsGridSwiper_<?php echo esc_js($uid); ?>);
    })();
    </script>
    <?php
  }

  protected function content_template() {
    ?>
    <div class="yachts-grid-widget">
      <p>Anteprima non disponibile. Salva e visualizza in frontend.</p>
    </div>
    <?php
  }
}
