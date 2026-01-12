<?php
class Elementor_Widget_Fixing_Sections extends \Elementor\Widget_Base {

  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);

    wp_register_style('fixing-sections-css', '/wp-content/plugins/elementor_addons/widgets/fixing-sections/fixing-sections.css');	  
  }
 public function get_style_depends() {
 return ['fixing-sections-css'];
 }

 public function get_name() { return 'fixing-sections'; }

 public function get_title() {
 return __('Sezioni Fisse', 'elementor_addon');
}

 public function get_icon() { return 'eicon-elementor'; }

 public function get_categories() { return ['general']; }

  protected function register_controls() {

    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Impostazioni', 'elementor-addon'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'eyebrow',
      [
        'label' => __('Eyebrow', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'BUILD',
      ]
    );

    $this->add_control(
      'title',
      [
        'label' => __('Titolo', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => 'Costruisci e personalizza il tuo yacht, progettato per te e le tue esigenze e soprattutto per come vivi il mare',
      ]
    );

    $this->add_control(
      'description',
      [
        'label' => __('Descrizione', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => 'Dalla scelta e gestione del posto barca alla progettazione e personalizzazione degli spazi a bordo, Vyba Yachts ti accompagna in ogni dettaglio del “tuo” porto sicuro: un home berth e un yacht costruito intorno al tuo stile di navigazione.',
      ]
    );

    $this->add_control(
      'button_text',
      [
        'label' => __('Testo Bottone', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'SCOPRI GLI YACHTS',
      ]
    );

    $this->add_control(
      'button_url',
      [
        'label' => __('Link Bottone', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::URL,
        'placeholder' => 'https://...',
      ]
    );

    $this->add_control(
      'image',
      [
        'label' => __('Immagine Destra', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'image_height',
      [
        'label' => __('Altezza immagine (px)', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => 750,
        'min' => 300,
        'max' => 1600,
      ]
    );

    $this->add_control(
      'sticky_top',
      [
        'label' => __('Offset sticky top (px)', 'elementor-addon'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => 80,
        'min' => 0,
        'max' => 300,
      ]
    );
	  
	$this->add_control(
	  'reverse_layout',
	  [
		'label' => __('Inverti layout (immagine a sinistra)', 'elementor-addon'),
		'type' => \Elementor\Controls_Manager::SWITCHER,
		'label_on' => __('Sì', 'elementor-addon'),
		'label_off' => __('No', 'elementor-addon'),
		'return_value' => 'yes',
		'default' => '',
	  ]
	);  

    $this->end_controls_section();
  }

  protected function render() {
    $s = $this->get_settings_for_display();

    $eyebrow = $s['eyebrow'] ?? '';
    $title = $s['title'] ?? '';
    $description = $s['description'] ?? '';
    $button_text = $s['button_text'] ?? '';
    $button_url = $s['button_url']['url'] ?? '';
    $is_external = !empty($s['button_url']['is_external']) ? ' target="_blank"' : '';
    $nofollow = !empty($s['button_url']['nofollow']) ? ' rel="nofollow"' : '';

    $image_url = $s['image']['url'] ?? '';
    $image_height = !empty($s['image_height']) ? (int)$s['image_height'] : 750;
    $sticky_top = isset($s['sticky_top']) ? (int)$s['sticky_top'] : 80;

	$reverse_layout = !empty($s['reverse_layout']) && $s['reverse_layout'] === 'yes';  
    $style_vars = sprintf(
      '--fs-image-height:%dpx; --fs-sticky-top:%dpx;',
      $image_height,
      $sticky_top
    );
    ?>

    <section 
      class="fs-section <?php echo $reverse_layout ? 'is-reversed' : ''; ?>"
      data-sticky-top="<?php echo esc_attr($sticky_top); ?>">
      <div class="fs-row">

        <!-- TESTO 60% -->
        <div class="fs-col fs-col-text">
          <div class="fs-text-wrap">
            <?php if ($eyebrow): ?>
              <div class="fs-eyebrow"><h6><?php echo $eyebrow; ?></h6></div>
            <?php endif; ?>

            <?php if ($title): ?>
              <h3 class="fs-title"><?php echo wp_kses_post(nl2br($title)); ?></h3>
            <?php endif; ?>

            <?php if ($description): ?>
              <div class="fs-desc"><p><?php echo wp_kses_post(nl2br($description)); ?></p></div>
            <?php endif; ?>

           <?php if ($button_text): ?>
			  <div class="fs-cta">

				<?php if (!empty($button_url)): ?>
				  <a class="hov-btn-link" href="<?php echo esc_url($button_url); ?>"<?php echo $is_external; ?><?php echo $nofollow; ?>>
					<div class="hov-btn-container">
					  <button class="hov-btn learn-more" type="button">
						<span class="circle" aria-hidden="true">
						  <span class="icon arrow"></span>
						</span>
						<span class="button-text"><?php echo esc_html($button_text); ?></span>
					  </button>
					</div>
				  </a>
				<?php else: ?>
				  <div class="hov-btn-container">
					<button class="hov-btn learn-more" type="button">
					  <span class="circle" aria-hidden="true">
						<span class="icon arrow"></span>
					  </span>
					  <span class="button-text"><?php echo esc_html($button_text); ?></span>
					</button>
				  </div>
				<?php endif; ?>

			  </div>
			<?php endif; ?>

          </div>
        </div>

        <!-- IMMAGINE 40% -->
        <div class="fs-col fs-col-media">
          <div class="fs-media-frame">
            <?php if ($image_url): ?>
              <img class="fs-media-img" src="<?php echo esc_url($image_url); ?>" alt="">
            <?php endif; ?>
          </div>
        </div>

      </div>
    </section>

	<script>
	(function () {

  function setupSection(section) {
    if (!section) return;

    // kill precedente init su questa istanza (se ricaricata in editor)
    if (section._fsCleanup) {
      section._fsCleanup();
      section._fsCleanup = null;
    }

    const textCol = section.querySelector(".fs-col-text");
    const textWrap = section.querySelector(".fs-text-wrap");
    const mediaFrame = section.querySelector(".fs-media-frame");
    const mediaImg = section.querySelector(".fs-media-img");

    if (!textCol || !textWrap || !mediaFrame) return;

    const stickyTop = parseInt(section.dataset.stickyTop || "0", 10);
    const forcedHeight = parseInt(section.dataset.imageHeight || "850", 10);

    // forza altezza coerente via inline
    mediaFrame.style.height = forcedHeight + "px";
    textCol.style.height = forcedHeight + "px";

    // Se l'immagine carica dopo, riallineiamo e refreshiamo
    if (mediaImg && !mediaImg.complete) {
      mediaImg.addEventListener("load", () => {
        mediaFrame.style.height = forcedHeight + "px";
        textCol.style.height = forcedHeight + "px";
        ScrollTrigger.refresh();
      }, { once: true });
    }

    const mm = gsap.matchMedia();

    const mqlCleanup = mm.add("(min-width: 841px)", () => {

      const getPinDistance = () => {
        const textH = textWrap.offsetHeight;
        const mediaH = mediaFrame.offsetHeight;
        return Math.max(0, mediaH - textH);
      };

      const setPinnedWidth = () => {
        const colRect = textCol.getBoundingClientRect();
        textWrap.style.width = colRect.width + "px";
      };

      setPinnedWidth();

      // ✅ trigger più preciso sulla colonna testo
      const st = ScrollTrigger.create({
        trigger: textCol,
        start: () => `top top+=${stickyTop}`,  // ✅ evita testo mozzato sopra
        end: () => `+=${getPinDistance()}`,
        pin: textWrap,
        pinSpacing: false,
        anticipatePin: 1,
        invalidateOnRefresh: true,
        onRefreshInit: () => {
          mediaFrame.style.height = forcedHeight + "px";
          textCol.style.height = forcedHeight + "px";
          setPinnedWidth();
        },
        onRefresh: () => setPinnedWidth(),
      });

      // ✅ PARALLAX su immagine
      if (mediaImg) {
        gsap.fromTo(mediaImg,
          { y: -80 },
          {
            y: 80,
            ease: "none",
            scrollTrigger: {
              trigger: mediaFrame,
              start: "top bottom",
              end: "bottom top",
              scrub: 0.5,
            }
          }
        );
      }

      const onResize = () => {
        setPinnedWidth();
        st.refresh();
      };

      window.addEventListener("resize", onResize);

      return () => {
        window.removeEventListener("resize", onResize);
        st.kill();
        textWrap.style.width = "";
      };
    });

    section._fsCleanup = () => {
      try { mqlCleanup && mqlCleanup(); } catch(e){}
      try { mm.kill(); } catch(e){}
    };
  }

  function initFixingSectionsGSAP() {
    if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;

    gsap.registerPlugin(ScrollTrigger);

    const sections = document.querySelectorAll(".fs-section");
    if (!sections.length) return;

    sections.forEach(setupSection);

    // doppio refresh leggero per Elementor/lazy layout
    ScrollTrigger.refresh();
    setTimeout(() => ScrollTrigger.refresh(), 120);
    setTimeout(() => ScrollTrigger.refresh(), 600);
  }

  // ✅ FIX: Aspetta che lo scroll sia stabile prima di inizializzare
  let isInitialized = false;
  let scrollTimeout;
  let initialScrollY = window.scrollY;
  
  function safeInit() {
    if (isInitialized) return;
    
    // Aspetta che lo scroll si sia stabilizzato
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
      if (!isInitialized) {
        isInitialized = true;
        initFixingSectionsGSAP();
        
        // Force refresh dopo init
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (typeof ScrollTrigger !== "undefined") {
              ScrollTrigger.refresh(true);
            }
          });
        });
      }
    }, 100);
  }

  // Aspetta il load completo + stabilizzazione scroll
  window.addEventListener("load", () => {
    // Se siamo già scrollati, aspetta che si stabilizzi
    if (window.scrollY > 0) {
      // Monitora lo scroll finché non si stabilizza
      let lastScroll = window.scrollY;
      const checkStable = setInterval(() => {
        if (Math.abs(window.scrollY - lastScroll) < 5) {
          clearInterval(checkStable);
          safeInit();
        }
        lastScroll = window.scrollY;
      }, 50);
      
      // Timeout di sicurezza
      setTimeout(() => {
        clearInterval(checkStable);
        safeInit();
      }, 1000);
    } else {
      // Se siamo in cima, init normale
      safeInit();
    }
  });

  // Fallback per DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      if (window.scrollY === 0) safeInit();
    });
  }

  // Elementor editor
  if (
    window.elementorFrontend &&
    window.elementorFrontend.hooks &&
    typeof window.elementorFrontend.hooks.addAction === "function"
  ) {
    window.elementorFrontend.hooks.addAction(
      "frontend/element_ready/fixing-sections.default",
      function () {
        initFixingSectionsGSAP();
      }
    );
  }

})();

	</script>

    <?php
  }

  protected function content_template() {}
}