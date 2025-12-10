<?php
class Elementor_Widget_Hov_Btn extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);

    wp_register_style('hov-btn-css', '/wp-content/plugins/elementor_addons/widgets/hov-btn/hov-btn.css');
	  
  }

  public function get_style_depends() {
    return ['hov-btn-css'];
  }

  public function get_name() { return 'hov-btn'; }

  public function get_title() {
    return __('Button Hover', 'elementor_addon');
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

    $this->add_control(
      'btn_text',
      [
        'label'   => __('Testo bottone', 'elementor-addon'),
        'type'    => \Elementor\Controls_Manager::TEXT,
        'default' => __('SCOPRI DI PIÙ', 'elementor-addon')
      ]
    );

    $this->add_control(
      'btn_link',
      [
        'label' => __('Link bottone', 'elementor-addon'),
        'type'  => \Elementor\Controls_Manager::URL,
      ]
    );
	  
	$this->add_control(
	  'btn_color',
	  [
		'label' => __('Colore bottone', 'elementor-addon'),
		'type' => \Elementor\Controls_Manager::SELECT,
		'default' => 'blue',
		'options' => [
		  'blue'  => __('Blu', 'elementor-addon'),
		  'white' => __('Bianco', 'elementor-addon'),
		],
	  ]
	);  

    $this->end_controls_section();
  }

  protected function render() {
    $settings  = $this->get_settings_for_display();
        $btn_text = !empty($settings['btn_text']) ? $settings['btn_text'] : __('SCOPRI DI PIÙ', 'elementor-addon');
	    $btn_color = !empty($settings['btn_color']) ? $settings['btn_color'] : 'blue';
    ?>

	<div class="hov-btn-container">
      <button class="hov-btn learn-more  <?php echo esc_attr($btn_color); ?>">
        <span class="circle" aria-hidden="true">
          <span class="icon arrow"></span>
        </span>
        <span class="button-text"><?php echo esc_html($btn_text); ?></span>
      </button>
    </div>    

    <?php
  }

  protected function _content_template() {}
}
