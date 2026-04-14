<?php 

/**
 * Get the URL of a page by its original (IT) slug, with Polylang translation support.
 */
function vyba_get_page_url($slug) {
    $page = get_page_by_path($slug);
    if (!$page) return home_url('/');

    $page_id = $page->ID;
    if (function_exists('pll_get_post')) {
        $translated_id = pll_get_post($page_id);
        if ($translated_id) $page_id = $translated_id;
    }
    return get_permalink($page_id);
}

/**
 * Get the title of a page by its original (IT) slug, with Polylang translation support.
 */
function vyba_get_page_title($slug) {
    $page = get_page_by_path($slug);
    if (!$page) return ucfirst($slug);

    $page_id = $page->ID;
    if (function_exists('pll_get_post')) {
        $translated_id = pll_get_post($page_id);
        if ($translated_id) $page_id = $translated_id;
    }
    return get_the_title($page_id);
}

if ( ! function_exists( 'wp_bootstrap_starter_setup' ) ) :

/**
 * Register all theme strings with Polylang for translation.
 */
function vyba_register_polylang_strings() {
    if (!function_exists('pll_register_string')) return;

    $group = 'Vyba Yachts';

    // Template single-yacht & single-charter
    pll_register_string('info_label', 'INFORMAZIONI', $group);
    pll_register_string('desc_imbarcazione', 'Descrizione imbarcazione', $group);
    pll_register_string('acquista_label', 'ACQUISTA', $group);
    pll_register_string('related_yachts', 'Potrebbero interessarti anche questi modelli di yachts', $group);
    pll_register_string('charter_label', 'CHARTER', $group);
    pll_register_string('related_charter', 'Potrebbero interessarti anche questi charter', $group);
    pll_register_string('scopri_btn', 'SCOPRI DI PIÙ', $group);
    pll_register_string('contatti_label', 'CONTATTI', $group);
    pll_register_string('cta_yacht', 'Richiedi informazioni su questa imbarcazione e mettiti in contatto con il venditore', $group);
    pll_register_string('cta_yacht_desc', 'Compila il form per ricevere scheda dettagliata, ulteriori foto, video e una consulenza dedicata. Ti ricontattiamo in breve per valutare insieme se questo è davvero lo yacht giusto per te.', $group);
    pll_register_string('cta_charter', 'Richiedi informazioni su questa imbarcazione e mettiti in contatto con noi', $group);
    pll_register_string('cta_charter_desc', 'Compila il form per ricevere scheda dettagliata, ulteriori foto, video e una consulenza dedicata. Ti ricontattiamo in breve per valutare insieme se questo è davvero il charter giusto per te.', $group);
    pll_register_string('prezzo_richiesta', 'Prezzo su richiesta', $group);
    pll_register_string('scheda_tecnica', 'SCARICA LA SCHEDA TECNICA', $group);
    pll_register_string('brochure', 'VEDI BROCHURE', $group);
    pll_register_string('contattaci_btn', 'CONTATTACI', $group);
    pll_register_string('price_day', '€ / giorno', $group);

    // Widget strings
    pll_register_string('no_yacht', 'Nessuno yacht disponibile.', $group);
    pll_register_string('no_charter', 'Nessun charter disponibile.', $group);
    pll_register_string('no_slide', 'Nessuna slide configurata.', $group);
    pll_register_string('no_posto_barca', 'Nessun posto barca disponibile.', $group);
    pll_register_string('no_image', 'Nessuna immagine', $group);
    pll_register_string('tutti_tab', 'TUTTI', $group);
    pll_register_string('vendita_tab', 'VENDITA', $group);
    pll_register_string('affitto_tab', 'AFFITTO', $group);
    pll_register_string('descrizione_label', 'Descrizione:', $group);
    pll_register_string('larghezza_label', 'Larghezza:', $group);
    pll_register_string('lunghezza_label', 'Lunghezza:', $group);
    pll_register_string('servizi_label', 'Servizi:', $group);
    pll_register_string('richiedi_info', 'RICHIEDI INFO', $group);

    // Footer
    pll_register_string('footer_rights', '©2026 tutti i diritti riservati', $group);
}
add_action('init', 'vyba_register_polylang_strings');

/**
 * Get the CF7 shortcode for the current language.
 */
function vyba_get_cf7_shortcode() {
    $form_it = get_option('vyba_cf7_form_it', 'fbe7748');
    $form_en = get_option('vyba_cf7_form_en', '');

    $form_id = $form_it;
    if (function_exists('pll_current_language') && pll_current_language() === 'en' && !empty($form_en)) {
        $form_id = $form_en;
    }

    return do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]');
}

/**
 * Admin page for CF7 form settings per language.
 */
add_action('admin_menu', function() {
    add_options_page('Vyba Forms', 'Vyba Forms', 'manage_options', 'vyba-forms', 'vyba_forms_settings_page');
});

function vyba_forms_settings_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['vyba_forms_save']) && check_admin_referer('vyba_forms_nonce')) {
        update_option('vyba_cf7_form_it', sanitize_text_field($_POST['vyba_cf7_form_it']));
        update_option('vyba_cf7_form_en', sanitize_text_field($_POST['vyba_cf7_form_en']));
        echo '<div class="notice notice-success"><p>Salvato!</p></div>';
    }

    $form_it = get_option('vyba_cf7_form_it', 'fbe7748');
    $form_en = get_option('vyba_cf7_form_en', '');
    ?>
    <div class="wrap">
        <h1>Vyba Forms - Contact Form 7 per lingua</h1>
        <form method="post">
            <?php wp_nonce_field('vyba_forms_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="vyba_cf7_form_it">Form ID - Italiano</label></th>
                    <td><input type="text" id="vyba_cf7_form_it" name="vyba_cf7_form_it" value="<?php echo esc_attr($form_it); ?>" class="regular-text" />
                    <p class="description">ID del form CF7 italiano (es. fbe7748)</p></td>
                </tr>
                <tr>
                    <th><label for="vyba_cf7_form_en">Form ID - English</label></th>
                    <td><input type="text" id="vyba_cf7_form_en" name="vyba_cf7_form_en" value="<?php echo esc_attr($form_en); ?>" class="regular-text" />
                    <p class="description">ID del form CF7 inglese. Lascia vuoto per usare quello italiano.</p></td>
                </tr>
            </table>
            <input type="submit" name="vyba_forms_save" class="button button-primary" value="Salva">
        </form>
    </div>
    <?php
}

function wp_bootstrap_starter_setup() {
	load_theme_textdomain( 'altera-starter', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menus( array(
		'primary-left' => esc_html__( 'Menu navbar sinistro', 'altera-starter' ),
		'primary-right' => esc_html__( 'Menu navbar destro', 'altera-starter' ),
		'footer-left' => esc_html__( 'Menu footer sinistro', 'altera-starter' ),
		'footer-right' => esc_html__( 'Menu footer destro', 'altera-starter' ),
	) );

	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'caption',
	) );
	add_theme_support( 'custom-background', apply_filters( 'wp_bootstrap_starter_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
	add_theme_support( 'customize-selective-refresh-widgets' );
    function wp_boostrap_starter_add_editor_styles() {
        add_editor_style( 'custom-editor-style.css' );
    }
    add_action( 'admin_init', 'wp_boostrap_starter_add_editor_styles' );

}
endif;
add_action( 'after_setup_theme', 'wp_bootstrap_starter_setup' );
function wp_bootstrap_starter_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wp_bootstrap_starter_content_width', 1170 );
}
add_action( 'after_setup_theme', 'wp_bootstrap_starter_content_width', 0 );


function wp_bootstrap_starter_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Sidebar', 'altera-starter' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here.', 'altera-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 1', 'altera-starter' ),
        'id'            => 'footer-1',
        'description'   => esc_html__( 'Add widgets here.', 'altera-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 2', 'altera-starter' ),
        'id'            => 'footer-2',
        'description'   => esc_html__( 'Add widgets here.', 'altera-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 3', 'altera-starter' ),
        'id'            => 'footer-3',
        'description'   => esc_html__( 'Add widgets here.', 'altera-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'wp_bootstrap_starter_widgets_init' );


function theme_gsap_script(){
    // The core GSAP library
    wp_enqueue_script( 'gsap-js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js', array(), false, true );
    // ScrollTrigger - with gsap.js passed as a dependency
    wp_enqueue_script( 'gsap-st', 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/ScrollTrigger.min.js', array('gsap-js'), false, true );
    // Your animation code file - with gsap.js passed as a dependency
    wp_enqueue_script( 'gsap-js2', get_template_directory_uri() . 'js/app.js', array('gsap-js'), false, true );
}

add_action( 'wp_enqueue_scripts', 'theme_gsap_script' );

function wp_bootstrap_starter_scripts() {

    wp_enqueue_style( 'altera-starter-bootstrap-css', get_template_directory_uri() . '/inc/assets/css/bootstrap.css');
	wp_enqueue_script('jquery');
    wp_enqueue_style( 'wp-style-css', get_template_directory_uri() . '/style.css' );
	wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/inc/assets/js/bootstrap.js');
	wp_enqueue_script('main-js', get_template_directory_uri() . '/inc/assets/js/main.js');
	
	// Enqueue sticky sidebar script only on single yacht pages
	if (is_singular('yacht')) {
		wp_enqueue_script('yacht-sticky-sidebar', get_template_directory_uri() . '/inc/assets/js/yacht-sticky-sidebar.js', array(), '1.0', true);
		wp_enqueue_script('yacht-gallery', get_template_directory_uri() . '/inc/assets/js/yacht-gallery.js', array(), '1.0', true);
	}
	
	// Enqueue sticky sidebar script only on single charter pages
	if (is_singular('charter')) {
		wp_enqueue_script('charter-sticky-sidebar', get_template_directory_uri() . '/inc/assets/js/charter-sticky-sidebar.js', array(), '1.0', true);
		wp_enqueue_script('charter-gallery', get_template_directory_uri() . '/inc/assets/js/charter-gallery.js', array(), '1.0', true);
	}
	
}
add_action( 'wp_enqueue_scripts', 'wp_bootstrap_starter_scripts' );

// Redirect /yachts/ to the Vendita page (Polylang-aware)
add_action('template_redirect', 'redirect_yachts_to_vendita');
function redirect_yachts_to_vendita() {
    if (is_post_type_archive('yacht') || (isset($_SERVER['REQUEST_URI']) && preg_match('#^(/[a-z]{2})?/yachts/?$#', $_SERVER['REQUEST_URI']))) {
        wp_redirect(vyba_get_page_url('vendita'), 301);
        exit;
    }
}
