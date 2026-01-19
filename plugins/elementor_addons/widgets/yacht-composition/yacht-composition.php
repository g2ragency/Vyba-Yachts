<?php
class Elementor_Widget_Yacht_Composition extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('yacht-composition-css', '/wp-content/plugins/elementor_addons/widgets/yacht-composition/yacht-composition.css');
  }

  public function get_style_depends() {
    return ['yacht-composition-css'];
  }

  public function get_name() { 
    return 'yacht-composition'; 
  }

  public function get_title() {
    return __('Yacht Composition', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-tabs'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  protected function register_controls() {

    // Tab 1
    $this->start_controls_section(
      'tab1_section',
      [
        'label' => __('Tab 1', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab1_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'SIDE',
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

    $this->end_controls_section();

    // Tab 2
    $this->start_controls_section(
      'tab2_section',
      [
        'label' => __('Tab 2', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab2_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'HIGH VIEW',
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

    $this->end_controls_section();

    // Tab 3
    $this->start_controls_section(
      'tab3_section',
      [
        'label' => __('Tab 3', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab3_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'UPPER DECK',
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

    $this->end_controls_section();

    // Tab 4
    $this->start_controls_section(
      'tab4_section',
      [
        'label' => __('Tab 4', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab4_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'MAIN DECK',
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

    $this->end_controls_section();

    // Tab 5
    $this->start_controls_section(
      'tab5_section',
      [
        'label' => __('Tab 5', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'tab5_title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'LOWER DECK',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'tab5_image',
      [
        'label'   => __('Immagine', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    
    $tabs = [
      [
        'title' => $settings['tab1_title'],
        'image' => $settings['tab1_image']['url'],
      ],
      [
        'title' => $settings['tab2_title'],
        'image' => $settings['tab2_image']['url'],
      ],
      [
        'title' => $settings['tab3_title'],
        'image' => $settings['tab3_image']['url'],
      ],
      [
        'title' => $settings['tab4_title'],
        'image' => $settings['tab4_image']['url'],
      ],
      [
        'title' => $settings['tab5_title'],
        'image' => $settings['tab5_image']['url'],
      ],
    ];

    $uid = 'yacht-composition-' . wp_rand(1000, 9999);
    ?>

    <div class="yacht-composition-widget <?php echo esc_attr($uid); ?>">
      
      <div class="composition-tabs">
        <?php foreach ($tabs as $index => $tab) : ?>
          <button class="composition-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="<?php echo $index; ?>">
            <?php echo esc_html($tab['title']); ?>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="composition-content">
        <?php foreach ($tabs as $index => $tab) : ?>
          <div class="composition-panel composition-accordion-item <?php echo $index === 0 ? 'active' : ''; ?>" data-panel="<?php echo $index; ?>">
            
            <div class="composition-accordion-header">
              <h4 class="composition-accordion-title"><?php echo esc_html($tab['title']); ?></h4>
              <div class="composition-accordion-icons">
                <span class="composition-accordion-arrow">
                  <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M35 1.37622L1 35.3762" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M1.43506 1L34.876 1.00005C35.1522 1.00005 35.376 1.22391 35.376 1.50005L35.3761 34.941" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </span>
              </div>
            </div>

            <?php if (!empty($tab['image'])) : ?>
              <div class="composition-image-desktop">
                <img src="<?php echo esc_url($tab['image']); ?>" alt="<?php echo esc_attr($tab['title']); ?>" />
              </div>
              <div class="composition-image-mobile">
                <img src="<?php echo esc_url($tab['image']); ?>" alt="<?php echo esc_attr($tab['title']); ?>" />
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

    </div>

    <script>
      (function() {
        var widget = document.querySelector('.<?php echo esc_js($uid); ?>');
        if (!widget) return;

        var tabs = widget.querySelectorAll('.composition-tab');
        var panels = widget.querySelectorAll('.composition-panel');
        var accordionHeaders = widget.querySelectorAll('.composition-accordion-header');

        // Desktop tabs functionality
        tabs.forEach(function(tab) {
          tab.addEventListener('click', function() {
            var tabIndex = this.getAttribute('data-tab');

            // Remove active class from all tabs and panels
            tabs.forEach(function(t) { t.classList.remove('active'); });
            panels.forEach(function(p) { p.classList.remove('active'); });

            // Add active class to clicked tab and corresponding panel
            this.classList.add('active');
            var activePanel = widget.querySelector('.composition-panel[data-panel="' + tabIndex + '"]');
            if (activePanel) {
              activePanel.classList.add('active');
            }
          });
        });

        // Mobile accordion functionality
        accordionHeaders.forEach(function(header) {
          header.addEventListener('click', function() {
            if (window.innerWidth > 768) return; // Only on mobile
            
            var item = this.closest('.composition-accordion-item');
            var isActive = item.classList.contains('active');

            // Close all items
            panels.forEach(function(panel) {
              panel.classList.remove('active');
            });

            // Toggle current item
            if (!isActive) {
              item.classList.add('active');
            }
          });
        });
      })();
    </script>

    <?php
  }
}
