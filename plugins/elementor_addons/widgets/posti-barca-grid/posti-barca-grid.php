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

    // Query tutti i posti barca (ignoriamo il filtro tipo, gestiamo via JS)
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

    $query = new \WP_Query($args);

    if (!$query->have_posts()) {
      echo '<p>Nessun posto barca disponibile.</p>';
      return;
    }

    $uid = $this->get_id();
    ?>

    <div class="posti-barca-grid-widget" id="posti-barca-<?php echo esc_attr($uid); ?>">
      
      <!-- Tabs Filtro -->
      <div class="posti-barca-tabs">
        <button class="posti-barca-tab active" data-filter="all">
          TUTTI
        </button>
        <button class="posti-barca-tab" data-filter="vendita">
          VENDITA
        </button>
        <button class="posti-barca-tab" data-filter="affitto">
          AFFITTO
        </button>
      </div>

      <div class="posti-barca-grid">

        <?php while ($query->have_posts()) : $query->the_post();
          $post_id = get_the_ID();
          $title     = get_the_title();
          $permalink = get_permalink();
          $excerpt   = get_the_excerpt();

          // Recupera il tipo (affitto o vendita)
          $terms = wp_get_post_terms($post_id, 'tipo_posto_barca');
          $tipo_slug = '';
          $tipo_label = '';
          if (!empty($terms) && !is_wp_error($terms)) {
            $tipo_slug = $terms[0]->slug;
            $tipo_label = $terms[0]->name;
          }

          // Recupera le categorie
          $categorie = wp_get_post_terms($post_id, 'categoria_posto_barca');
          $categoria_label = '';
          if (!empty($categorie) && !is_wp_error($categorie)) {
            $categoria_label = $categorie[0]->name;
          }

          // Recupera i campi ACF
          $descrizione = get_field('posto_barca_descrizione', $post_id);
          $larghezza = get_field('posto_barca_larghezza', $post_id);
          $lunghezza = get_field('posto_barca_lunghezza', $post_id);
          $servizi = get_field('posto_barca_servizi', $post_id);

          // Immagine in evidenza
          $thumb_id = get_post_thumbnail_id($post_id);
          $img_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
          $alt = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
        ?>

          <article class="posto-barca-grid-card" data-tipo="<?php echo esc_attr($tipo_slug); ?>">

            <div class="posto-barca-grid-card__media">
              <?php if ($tipo_label) : ?>
                <span class="posto-barca-grid-card__label">
                  <?php echo esc_html(strtoupper($tipo_label)); ?>
                </span>
              <?php endif; ?>
              
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

              <div class="posto-barca-grid-card__details">
                <?php if ($descrizione) : ?>
                  <div class="posto-barca-detail">
                    <span class="detail-label">Descrizione:</span>
                    <span class="detail-value"><?php echo esc_html($descrizione); ?></span>
                  </div>
                <?php endif; ?>

                <?php if ($larghezza) : ?>
                  <div class="posto-barca-detail">
                    <span class="detail-label">Larghezza:</span>
                    <span class="detail-value"><?php echo esc_html($larghezza); ?></span>
                  </div>
                <?php endif; ?>

                <?php if ($lunghezza) : ?>
                  <div class="posto-barca-detail">
                    <span class="detail-label">Lunghezza:</span>
                    <span class="detail-value"><?php echo esc_html($lunghezza); ?></span>
                  </div>
                <?php endif; ?>

                <?php if ($servizi) : ?>
                  <div class="posto-barca-detail">
                    <span class="detail-label">Servizi:</span>
                    <span class="detail-value"><?php echo esc_html($servizi); ?></span>
                  </div>
                <?php endif; ?>
              </div>

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

    <script>
    (function() {
      var widget = document.getElementById('posti-barca-<?php echo esc_js($uid); ?>');
      if (!widget) return;

      var tabs = widget.querySelectorAll('.posti-barca-tab');
      var cards = widget.querySelectorAll('.posto-barca-grid-card');

      tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
          var filter = this.getAttribute('data-filter');

          // Update active tab
          tabs.forEach(function(t) { t.classList.remove('active'); });
          this.classList.add('active');

          // Filter cards
          cards.forEach(function(card) {
            var tipo = card.getAttribute('data-tipo');
            
            if (filter === 'all') {
              card.style.display = 'flex';
            } else if (tipo === filter) {
              card.style.display = 'flex';
            } else {
              card.style.display = 'none';
            }
          });
        });
      });
    })();
    </script>
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
