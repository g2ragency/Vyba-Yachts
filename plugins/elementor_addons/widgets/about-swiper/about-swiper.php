<?php
class Elementor_Widget_About_Swiper extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('about-swiper-css', '/wp-content/plugins/elementor_addons/widgets/about-swiper/about-swiper.css');
  }

  public function get_style_depends() {
    return ['about-swiper-css'];
  }

  public function get_name() { 
    return 'about-swiper'; 
  }

  public function get_title() {
    return __('About Swiper', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-slider-push'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  protected function register_controls() {

    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Impostazioni', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    // Repeater per slide statistiche
    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'numero',
      [
        'label'       => __('Numero', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => '42',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'titolo',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'IMBARCAZIONI VALUTATE DURANTE L\'ANNO',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'descrizione',
      [
        'label'       => __('Descrizione', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXTAREA,
        'default'     => 'Tra perizie pre-acquisto, controlli su yacht nuovi e prove in mare, accompagnando tutti gli armatori',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'slides',
      [
        'label'       => __('Slide', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'default'     => [
          [
            'numero'      => '42',
            'titolo'      => 'IMBARCAZIONI VALUTATE DURANTE L\'ANNO',
            'descrizione' => 'Tra perizie pre-acquisto, controlli su yacht nuovi e prove in mare, accompagnando tutti gli armatori',
          ],
          [
            'numero'      => '1.450',
            'titolo'      => 'MIGLIA DI NAVIGAZIONE OGNI MESE DELL\'ANNO',
            'descrizione' => 'La nostra consulenza nasce dall\'esperienza vissuta in mare, non solo dai dati su una scheda tecnica.',
          ],
          [
            'numero'      => '15',
            'titolo'      => 'PARTER TECNICI STRATEGICI E PROFESSIONALI',
            'descrizione' => 'Una rete di contatti selezionati in base al miglior rapporto qualità-prezzo, non semplicemente il più economico.',
          ],
        ],
        'title_field' => '{{{ numero }}} - {{{ titolo }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $slides   = $settings['slides'] ?? [];

    if (empty($slides)) {
      echo '<p>Nessuna slide configurata.</p>';
      return;
    }

    ?>
    <div class="about-swiper-wrapper">
      <div class="swiper about-swiper">
        <div class="swiper-wrapper">
          <?php foreach ($slides as $slide): ?>
            <div class="swiper-slide">
              <div class="about-stat">
                <div class="about-stat__numero"><?php echo esc_html($slide['numero']); ?></div>
                <h4 class="about-stat__titolo"><?php echo esc_html($slide['titolo']); ?></h4>
                <p class="about-stat__descrizione"><?php echo esc_html($slide['descrizione']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Scrollbar e Navigation sulla stessa riga -->
      <div class="swiper-controls">
        <div class="swiper-scrollbar about-swiper-scrollbar"></div>
        <div class="swiper-navigation">
          <div class="swiper-button-prev about-swiper-button-prev"></div>
          <div class="swiper-button-next about-swiper-button-next"></div>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (typeof Swiper === "undefined") return;

      const aboutSwiper = new Swiper(".about-swiper", {
        slidesPerView: 1,
        spaceBetween: 60,
        loop: false,
        navigation: {
          nextEl: ".about-swiper-button-next",
          prevEl: ".about-swiper-button-prev",
        },
        scrollbar: {
          el: ".about-swiper-scrollbar",
          draggable: true,
        },
        breakpoints: {
          768: {
            slidesPerView: 2,
            spaceBetween: 40,
          },
          1024: {
            slidesPerView: 2.5,
            spaceBetween: 140,
          },
        },
      });
    });
    </script>
    <?php
  }

  protected function content_template() {
    ?>
    <#
    if (!settings.slides || settings.slides.length === 0) {
      #><p>Nessuna slide configurata.</p><#
      return;
    }
    #>
    <div class="about-swiper-wrapper">
      <div class="swiper about-swiper">
        <div class="swiper-wrapper">
          <# _.each(settings.slides, function(slide) { #>
            <div class="swiper-slide">
              <div class="about-stat">
                <div class="about-stat__numero">{{{ slide.numero }}}</div>
                <h3 class="about-stat__titolo">{{{ slide.titolo }}}</h3>
                <p class="about-stat__descrizione">{{{ slide.descrizione }}}</p>
              </div>
            </div>
          <# }); #>
        </div>
      </div>

      <div class="swiper-controls">
        <div class="swiper-scrollbar about-swiper-scrollbar"></div>
        <div class="swiper-navigation">
          <div class="swiper-button-prev about-swiper-button-prev"></div>
          <div class="swiper-button-next about-swiper-button-next"></div>
        </div>
      </div>
    </div>
    <?php
  }
}
