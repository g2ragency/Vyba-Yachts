<?php
class Elementor_Widget_Hov_Servizi extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);

    wp_register_style('hov-servizi-css', '/wp-content/plugins/elementor_addons/widgets/hov-servizi/hov-servizi.css');	  
  }
 public function get_style_depends() {
 return ['hov-servizi-css'];
 }

 public function get_name() { return 'hov-servizi'; }

 public function get_title() {
 return __('Servizi Hover', 'elementor_addon');
}

 public function get_icon() { return 'eicon-elementor'; }

 public function get_categories() { return ['general']; }

 protected function _register_controls() {

    $this->start_controls_section(
        'content_section',
        [
            'label' => __('Impostazioni', 'elementor-addon'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
    );

    // Repeater per la lista dei servizi
    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
        'service_title',
        [
            'label' => __('Titolo Servizio', 'elementor-addon'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'NUOVO SERVIZIO',
            'label_block' => true,
        ]
    );

    $repeater->add_control(
        'service_link',
        [
            'label' => __('Link', 'elementor-addon'),
            'type' => \Elementor\Controls_Manager::URL,
            'placeholder' => 'https://il-tuo-link.com',
            'show_external' => true,
            'default' => [
                'url' => '#',
                'is_external' => true,
                'nofollow' => true,
            ],
        ]
    );
    
    // Controllo per l'immagine associata all'hover
    $repeater->add_control(
        'service_image',
        [
            'label' => __('Immagine (Hover)', 'elementor-addon'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]
    );

    $this->add_control(
        'service_list',
        [
            'label' => __('Lista Servizi', 'elementor-addon'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_fields(),
            'title_field' => '{{{ service_title }}}',
        ]
    );
    
    // Controllo per l'immagine di default (se non è in hover su nessun elemento)
    $this->add_control(
        'default_image',
        [
            'label' => __('Immagine di Default', 'elementor-addon'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'description' => 'Questa immagine viene mostrata quando non c\'è nessun hover.',
        ]
    );

    $this->end_controls_section();
}

 protected function render() {
    $settings = $this->get_settings_for_display();
    $list_items = $settings['service_list'];
    $default_image_url = $settings['default_image']['url'];
    
    $unique_id = 'hov-servizi-' . $this->get_id(); // ID univoco per il widget
    ?>

    <div class="hov-servizi-wrapper" id="<?php echo esc_attr($unique_id); ?>">
        <div class="hov-servizi-content">
            <?php
            if ($list_items) {
                foreach ($list_items as $item) {
                    $item_id = $item['_id'];
                    $title = $item['service_title'];
                    $link = $item['service_link']['url'];
                    $image_url = $item['service_image']['url'];
                    ?>
                    
                    <a 
                        href="<?php echo esc_url($link); ?>"
                        class="service-item"
                        data-image-src="<?php echo esc_url($image_url); ?>"
                    >
                        <span class="service-title"><?php echo esc_html($title); ?></span>
                        <span class="service-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" viewBox="0 0 37 37" fill="none">
<path d="M35 1.37622L1 35.3762" stroke="#161F48" style="stroke:#161F48;stroke:color(display-p3 0.0863 0.1216 0.2824);stroke-opacity:1;" stroke-width="2" stroke-linecap="round"/>
<path d="M1.43506 1L34.876 1.00005C35.1522 1.00005 35.376 1.22391 35.376 1.50005L35.3761 34.941" stroke="#161F48" style="stroke:#161F48;stroke:color(display-p3 0.0863 0.1216 0.2824);stroke-opacity:1;" stroke-width="2" stroke-linecap="round"/>
</svg>
                        </span>
                    </a>
                    <?php
                }
            }
            ?>
        </div>

        <div class="hov-servizi-image-container">
            <img 
                src="<?php echo esc_url($default_image_url); ?>"
                alt="Immagine di Default"
                class="hover-image"
            />
        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var $widget = $('#<?php echo esc_attr($unique_id); ?>');
        var $image = $widget.find('.hover-image');
        var defaultImageSrc = $image.attr('src');

        // Funzione per il cambio immagine all'hover
        $widget.find('.service-item').on('mouseenter', function() {
            var newImageSrc = $(this).data('image-src');
            if (newImageSrc) {
                $image.attr('src', newImageSrc);
            }
        }).on('mouseleave', function() {
            // Torna all'immagine di default quando il mouse esce
            $image.attr('src', defaultImageSrc);
        });
    });
    </script>
    <?php
}

 protected function _content_template() {

 }
}