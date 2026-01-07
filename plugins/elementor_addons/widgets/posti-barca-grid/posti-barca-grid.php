<?php
class Elementor_Widget_Posti_Barca_Grid extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
	  
    wp_register_style('posti-barca-grid-css', '/wp-content/plugins/elementor_addons/widgets/posti-barca-grid/posti-barca-grid.css');
  }

  public function get_style_depends() {
    return ['posti-barca-grid-css'];
  }

  public function get_name() { 
    return 'posti-barca-grid'; 
  }

  public function get_title() {
    return __('Posti Barca Grid', 'elementor_addon');
  }

  public function get_icon() { 
    return 'eicon-gallery-grid'; 
  }

  public function get_categories() { 
    return ['general']; 
  }

  /**
   * Recupera opzioni posti barca in ordine alfabetico per il select.
   */
  private function get_posti_barca_options() {
    $options = [];

    $q = new \WP_Query([
      'post_type'      => 'posto_barca',
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
      'tipo_posto_barca',
      [
        'label'       => __('Tipo Posto Barca', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::SELECT,
        'options'     => [
          'all'     => __('Tutti', 'elementor-addon'),
          'affitto' => __('Affitto', 'elementor-addon'),
          'vendita' => __('Vendita', 'elementor-addon'),
        ],
        'default'     => 'all',
        'description' => __('Scegli se mostrare posti barca in affitto, in vendita o tutti', 'elementor-addon'),
      ]
    );

    $this->add_control(
      'exclude_posti',
      [
        'label'       => __('Escludi Posti Barca', 'elementor-addon'),
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'multiple'    => true,
        'options'     => $this->get_posti_barca_options(),
        'label_block' => true,
        'description' => __('Seleziona i posti barca da escludere dalla griglia', 'elementor-addon'),
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $exclude_ids = !empty($settings['exclude_posti']) ? $settings['exclude_posti'] : [];
    $tipo = $settings['tipo_posto_barca'] ?? 'all';

    // Query posti barca
    $args = [
      'post_type'      => 'posto_barca',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    ];

    if (!empty($exclude_ids)) {
      $args['post__not_in'] = $exclude_ids;
    }

    // Filtra per tipo se non è "all"
    if ($tipo !== 'all') {
      $args['tax_query'] = [
        [
          'taxonomy' => 'tipo_posto_barca',
          'field'    => 'slug',
          'terms'    => $tipo,
        ],
      ];
    }

    $query = new \WP_Query($args);

    if (!$query->have_posts()) {
      echo '<p>Nessun posto barca disponibile.</p>';
      return;
    }

    $uid = $this->get_id();
    ?>

    <div class="posti-barca-grid-widget">
      <div class="posti-barca-grid">

        <?php while ($query->have_posts()) : $query->the_post();
          $post_id = get_the_ID();
          $title     = get_the_title();
          $permalink = get_permalink();
          $excerpt   = get_the_excerpt();

          // Recupera il tipo (affitto o vendita)
          $terms = wp_get_post_terms($post_id, 'tipo_posto_barca');
          $tipo_label = '';
          if (!empty($terms) && !is_wp_error($terms)) {
            $tipo_label = $terms[0]->name;
          }

          // Recupera le categorie
          $categorie = wp_get_post_terms($post_id, 'categoria_posto_barca');
          $categoria_label = '';
          if (!empty($categorie) && !is_wp_error($categorie)) {
            $categoria_label = $categorie[0]->name;
          }

          // Immagine in evidenza
          $thumb_id = get_post_thumbnail_id($post_id);
          $img_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
          $alt = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
        ?>

          <article class="posto-barca-grid-card">

            <div class="posto-barca-grid-card__media">
              <?php if ($img_url) : ?>
                <img
                  src="<?php echo esc_url($img_url); ?>"
                  alt="<?php echo esc_attr($alt ?: $title); ?>"
                  loading="lazy"
                />
              <?php else : ?>
                <div class="posto-barca-grid-card__placeholder">
                  <span>Nessuna immagine</span>
                </div>
              <?php endif; ?>
            </div>

            <div class="posto-barca-grid-card__body">

              <?php if ($categoria_label) : ?>
                <h6 class="posto-barca-grid-card__categoria">
                  <?php echo esc_html($categoria_label); ?>
                </h6>
              <?php endif; ?>

              <h5 class="posto-barca-grid-card__title">
                <?php echo esc_html($title); ?>
              </h5>

              <?php if ($excerpt) : ?>
                <div class="posto-barca-grid-card__excerpt">
                  <?php echo wp_kses_post($excerpt); ?>
                </div>
              <?php endif; ?>

              <div class="posto-barca-grid-card__cta">
                <a class="hov-btn learn-more" href="<?php echo esc_url($permalink); ?>">
                  <span class="circle" aria-hidden="true">
                    <span class="icon arrow"></span>
                  </span>
                  <span class="button-text">RICHIEDI INFO</span>
                </a>
              </div>

            </div>
          </article>

        <?php endwhile; wp_reset_postdata(); ?>

      </div>
    </div>
    <?php
  }

  protected function content_template() {
    ?>
    <div class="posti-barca-grid-widget">
      <p>Anteprima non disponibile. Salva e visualizza in frontend.</p>
    </div>
    <?php
  }
}
