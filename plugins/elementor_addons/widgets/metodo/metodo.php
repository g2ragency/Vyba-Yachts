<?php
class Elementor_Widget_Metodo extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('metodo-css', '/wp-content/plugins/elementor_addons/widgets/metodo/metodo.css');
  }

  public function get_style_depends() {
    return ['metodo-css'];
  }

  public function get_name() { 
    return 'metodo'; 
  }

  public function get_title() {
    return __('Metodo', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-tabs'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  protected function register_controls() {

    // Scheda 1
    $this->start_controls_section(
      'tab1_section',
      [
        'label' => __('Scheda 1', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab1_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'AVVICINAMENTO E ASCOLTO',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'tab1_image',
      [
        'label'   => __('Immagine', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'tab1_description',
      [
        'label'       => __('Descrizione', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXTAREA,
        'default'     => 'Partiamo dal tuo modo di vivere il mare: tipo di crociere, equipaggio, budget, tempi, aspettative. Prima di parlare di barche, parliamo di te, delle tue abitudini, di ciò che ti fa sentire davvero a tuo agio a bordo. Solo così possiamo capire quale yacht, quale posto barca e quale progetto rispecchiano davvero il tuo stile di navigazione e la tua idea di libertà.',
        'rows'        => 5,
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Scheda 2
    $this->start_controls_section(
      'tab2_section',
      [
        'label' => __('Scheda 2', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab2_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'ANALISI DELLE OPPORTUNITÀ',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'tab2_image',
      [
        'label'   => __('Immagine', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'tab2_description',
      [
        'label'       => __('Descrizione', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXTAREA,
        'default'     => 'Analizziamo insieme le diverse opportunità disponibili sul mercato.',
        'rows'        => 5,
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Scheda 3
    $this->start_controls_section(
      'tab3_section',
      [
        'label' => __('Scheda 3', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab3_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'SELEZIONE E NEGOZIAZIONE',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'tab3_image',
      [
        'label'   => __('Immagine', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'tab3_description',
      [
        'label'       => __('Descrizione', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXTAREA,
        'default'     => 'Ti assistiamo nella selezione e nella negoziazione delle migliori opportunità.',
        'rows'        => 5,
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Scheda 4
    $this->start_controls_section(
      'tab4_section',
      [
        'label' => __('Scheda 4', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab4_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'TUTELA NEL TEMPO',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'tab4_image',
      [
        'label'   => __('Immagine', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'tab4_description',
      [
        'label'       => __('Descrizione', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXTAREA,
        'default'     => 'Ti accompagniamo nel tempo con assistenza continua e supporto dedicato.',
        'rows'        => 5,
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    ?>
    <div class="metodo-wrapper">
      <!-- Tabs Navigation -->
      <div class="metodo-tabs">
        <div class="metodo-tab active" data-tab="1">
          <span><?php echo esc_html($settings['tab1_title']); ?></span>
        </div>
        <div class="metodo-tab" data-tab="2">
          <span><?php echo esc_html($settings['tab2_title']); ?></span>
        </div>
        <div class="metodo-tab" data-tab="3">
          <span><?php echo esc_html($settings['tab3_title']); ?></span>
        </div>
        <div class="metodo-tab" data-tab="4">
          <span><?php echo esc_html($settings['tab4_title']); ?></span>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="metodo-content">
        <div class="metodo-panel active" data-panel="1">
          <div class="metodo-panel__image">
            <img src="<?php echo esc_url($settings['tab1_image']['url']); ?>" alt="<?php echo esc_attr($settings['tab1_title']); ?>">
          </div>
          <div class="metodo-panel__text" data-title="<?php echo esc_attr($settings['tab1_title']); ?>">
            <p><?php echo esc_html($settings['tab1_description']); ?></p>
          </div>
        </div>

        <div class="metodo-panel" data-panel="2">
          <div class="metodo-panel__image">
            <img src="<?php echo esc_url($settings['tab2_image']['url']); ?>" alt="<?php echo esc_attr($settings['tab2_title']); ?>">
          </div>
          <div class="metodo-panel__text" data-title="<?php echo esc_attr($settings['tab2_title']); ?>">
            <p><?php echo esc_html($settings['tab2_description']); ?></p>
          </div>
        </div>

        <div class="metodo-panel" data-panel="3">
          <div class="metodo-panel__image">
            <img src="<?php echo esc_url($settings['tab3_image']['url']); ?>" alt="<?php echo esc_attr($settings['tab3_title']); ?>">
          </div>
          <div class="metodo-panel__text" data-title="<?php echo esc_attr($settings['tab3_title']); ?>">
            <p><?php echo esc_html($settings['tab3_description']); ?></p>
          </div>
        </div>

        <div class="metodo-panel" data-panel="4">
          <div class="metodo-panel__image">
            <img src="<?php echo esc_url($settings['tab4_image']['url']); ?>" alt="<?php echo esc_attr($settings['tab4_title']); ?>">
          </div>
          <div class="metodo-panel__text" data-title="<?php echo esc_attr($settings['tab4_title']); ?>">
            <p><?php echo esc_html($settings['tab4_description']); ?></p>
          </div>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
      const tabs = document.querySelectorAll('.metodo-tab');
      const panels = document.querySelectorAll('.metodo-panel');

      // Desktop tabs functionality
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          const tabIndex = this.getAttribute('data-tab');

          // Remove active class from all tabs and panels
          tabs.forEach(t => t.classList.remove('active'));
          panels.forEach(p => p.classList.remove('active'));

          // Add active class to clicked tab and corresponding panel
          this.classList.add('active');
          document.querySelector(`.metodo-panel[data-panel="${tabIndex}"]`).classList.add('active');
        });
      });

      // Mobile accordion functionality
      function initMobileAccordion() {
        if (window.innerWidth <= 768) {
          panels.forEach(panel => {
            const textDiv = panel.querySelector('.metodo-panel__text');
            
            textDiv.addEventListener('click', function() {
              const isActive = panel.classList.contains('active');
              
              // Chiudi tutti i panel
              panels.forEach(p => p.classList.remove('active'));
              
              // Apri quello cliccato se non era già aperto
              if (!isActive) {
                panel.classList.add('active');
              }
            });
          });
        }
      }

      initMobileAccordion();
    });
    </script>
    <?php
  }

  protected function content_template() {
    ?>
    <div class="metodo-wrapper">
      <div class="metodo-tabs">
        <div class="metodo-tab active" data-tab="1">
          <span>{{{ settings.tab1_title }}}</span>
        </div>
        <div class="metodo-tab" data-tab="2">
          <span>{{{ settings.tab2_title }}}</span>
        </div>
        <div class="metodo-tab" data-tab="3">
          <span>{{{ settings.tab3_title }}}</span>
        </div>
        <div class="metodo-tab" data-tab="4">
          <span>{{{ settings.tab4_title }}}</span>
        </div>
      </div>

      <div class="metodo-content">
        <div class="metodo-panel active" data-panel="1">
          <div class="metodo-panel__image">
            <img src="{{ settings.tab1_image.url }}" alt="{{ settings.tab1_title }}">
          </div>
          <div class="metodo-panel__text">
            <p>{{{ settings.tab1_description }}}</p>
          </div>
        </div>

        <div class="metodo-panel" data-panel="2">
          <div class="metodo-panel__image">
            <img src="{{ settings.tab2_image.url }}" alt="{{ settings.tab2_title }}">
          </div>
          <div class="metodo-panel__text">
            <p>{{{ settings.tab2_description }}}</p>
          </div>
        </div>

        <div class="metodo-panel" data-panel="3">
          <div class="metodo-panel__image">
            <img src="{{ settings.tab3_image.url }}" alt="{{ settings.tab3_title }}">
          </div>
          <div class="metodo-panel__text">
            <p>{{{ settings.tab3_description }}}</p>
          </div>
        </div>

        <div class="metodo-panel" data-panel="4">
          <div class="metodo-panel__image">
            <img src="{{ settings.tab4_image.url }}" alt="{{ settings.tab4_title }}">
          </div>
          <div class="metodo-panel__text">
            <p>{{{ settings.tab4_description }}}</p>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
}
