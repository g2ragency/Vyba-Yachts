<?php
class Elementor_Widget_Accordion_Perizie extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('accordion-perizie-css', '/wp-content/plugins/elementor_addons/widgets/accordion-perizie/accordion-perizie.css');
  }

  public function get_style_depends() {
    return ['accordion-perizie-css'];
  }

  public function get_name() { 
    return 'accordion-perizie'; 
  }

  public function get_title() {
    return __('Accordion Perizie', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-accordion'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  protected function register_controls() {

    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Perizie', 'elementor-addon'),
        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'title',
      [
        'label'       => __('Titolo', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::TEXT,
        'default'     => 'Perizie Pre-Acquisto',
        'label_block' => true,
      ]
    );

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

    $repeater->add_control(
      'description',
      [
        'label'       => __('Descrizione', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::WYSIWYG,
        'default'     => 'Descrizione della perizia.',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'accordion_items',
      [
        'label'       => __('Elementi Accordion', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'default'     => [
          [
            'title'       => 'Perizie Pre-Acquisto',
            'description' => 'Descrizione perizia pre-acquisto.',
            'icon'        => '',
          ],
          [
            'title'       => 'Project Management',
            'description' => 'Descrizione project management.',
            'icon'        => '',
          ],
          [
            'title'       => 'Perizie per Yacht Nuovi',
            'description' => 'Descrizione perizie yacht nuovi.',
            'icon'        => '',
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $items = $settings['accordion_items'];

    if (empty($items)) {
      return;
    }

    $uid = 'accordion-perizie-' . wp_rand(1000, 9999);
    ?>

    <div class="accordion-perizie-widget <?php echo esc_attr($uid); ?>">
      <?php foreach ($items as $index => $item) : 
        $item_id = 'accordion-item-' . $uid . '-' . $index;
        $is_first = ($index === 0);
      ?>
        <div class="accordion-item <?php echo $is_first ? 'active' : ''; ?>" data-item="<?php echo esc_attr($item_id); ?>">
          
          <div class="accordion-header">
            <div class="accordion-header-content">
              <?php if (!empty($item['image']['url'])) : ?>
                <div class="accordion-thumbnail">
                  <img src="<?php echo esc_url($item['image']['url']); ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                </div>
              <?php endif; ?>
              
              <h4 class="accordion-title"><?php echo esc_html($item['title']); ?></h4>
            </div>
            
            <div class="accordion-icons">
              <span class="accordion-arrow">
                <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M35 1.37622L1 35.3762" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <path d="M1.43506 1L34.876 1.00005C35.1522 1.00005 35.376 1.22391 35.376 1.50005L35.3761 34.941" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="accordion-content">
            <?php echo wp_kses_post($item['description']); ?>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

    <script>
      (function() {
        var accordionEl = document.querySelector('.<?php echo esc_js($uid); ?>');
        if (!accordionEl) return;

        var items = accordionEl.querySelectorAll('.accordion-item');

        items.forEach(function(item) {
          var header = item.querySelector('.accordion-header');
          
          header.addEventListener('click', function() {
            var isActive = item.classList.contains('active');

            // Chiudi tutti gli altri
            items.forEach(function(otherItem) {
              if (otherItem !== item) {
                otherItem.classList.remove('active');
              }
            });

            // Toggle corrente
            if (isActive) {
              item.classList.remove('active');
            } else {
              item.classList.add('active');
            }
          });
        });
      })();
    </script>

    <?php
  }
}
