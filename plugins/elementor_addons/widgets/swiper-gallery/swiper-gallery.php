<?php
class Elementor_Widget_Swiper_Gallery extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('swiper-gallery-css', '/wp-content/plugins/elementor_addons/widgets/swiper-gallery/swiper-gallery.css');
  }

  public function get_style_depends() {
    return ['swiper-gallery-css'];
  }

  public function get_name() { 
    return 'swiper-gallery'; 
  }

  public function get_title() {
    return __('Swiper Gallery', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-gallery-masonry'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  protected function register_controls() {

    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Galleria', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'image',
      [
        'label'   => __('Immagine', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'gallery_images',
      [
        'label'       => __('Immagini Galleria', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'default'     => [
          [
            'image' => [
              'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
          ],
          [
            'image' => [
              'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
          ],
        ],
        'title_field' => 'Immagine',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $images = $settings['gallery_images'];

    if (empty($images)) {
      return;
    }

    $uid = 'swiper-gallery-' . wp_rand(1000, 9999);
    ?>

    <div class="swiper-gallery-widget <?php echo esc_attr($uid); ?>">
      <div class="swiper swiper-gallery-main">
        <div class="swiper-wrapper">
          <?php foreach ($images as $item) : 
            if (!empty($item['image']['url'])) :
          ?>
            <div class="swiper-slide">
              <div class="gallery-slide-image">
                <img src="<?php echo esc_url($item['image']['url']); ?>" alt="" loading="lazy" />
              </div>
            </div>
          <?php 
            endif;
          endforeach; 
          ?>
        </div>
      </div>
      
      <div class="gallery-scrollbar">
        <div class="swiper-scrollbar"></div>
        <div class="gallery-navigation">
          <div class="swiper-button-prev gallery-prev"></div>
          <div class="swiper-button-next gallery-next"></div>
        </div>
      </div>
    </div>

    <script>
      (function() {
        var galleryEl = document.querySelector('.<?php echo esc_js($uid); ?>');
        if (!galleryEl) return;

        function initGallery() {
          var swiperEl = galleryEl.querySelector('.swiper-gallery-main');
          if (!swiperEl) return;

          var swiper = new Swiper(swiperEl, {
            slidesPerView: 1.2,
            spaceBetween: 20,
            centeredSlides: false,
            loop: false,
            navigation: {
              nextEl: galleryEl.querySelector('.gallery-next'),
              prevEl: galleryEl.querySelector('.gallery-prev'),
            },
            scrollbar: {
              el: galleryEl.querySelector('.gallery-scrollbar .swiper-scrollbar'),
              draggable: true,
            },
            breakpoints: {
              768: {
                slidesPerView: 1.8,
                spaceBetween: 10,
              },
              1024: {
                slidesPerView: 1.5,
                spaceBetween: 20,
              },
            }
          });
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initGallery);
        } else {
          initGallery();
        }

        window.addEventListener('load', initGallery);
      })();
    </script>

    <?php
  }
}
