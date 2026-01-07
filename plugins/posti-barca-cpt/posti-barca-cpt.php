<?php
/**
 * Plugin Name: Posti Barca CPT
 * Description: Custom Post Type per Posti Barca con categoria obbligatoria (Affitto/Vendita).
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

// Carica CSS e JS per il popup form
add_action('wp_enqueue_scripts', 'posti_barca_enqueue_popup_assets');

function posti_barca_enqueue_popup_assets() {
    wp_enqueue_style(
        'posti-barca-popup-css',
        plugin_dir_url(__FILE__) . 'popup-form.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script(
        'posti-barca-popup-js',
        plugin_dir_url(__FILE__) . 'popup-form.js',
        array(),
        '1.0.0',
        true
    );
    
    // Recupera lo shortcode CF7 dalle opzioni
    $cf7_shortcode = get_option('posti_barca_cf7_shortcode', '');
    
    // Renderizza lo shortcode
    $rendered_form = '';
    if (!empty($cf7_shortcode)) {
        ob_start();
        echo do_shortcode($cf7_shortcode);
        $rendered_form = ob_get_clean();
    }
    
    wp_localize_script('posti-barca-popup-js', 'postoBarcaData', array(
        'shortcode' => $rendered_form
    ));
}

// Aggiungi pagina impostazioni nel menu admin
add_action('admin_menu', 'posti_barca_add_settings_page');

function posti_barca_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=posto_barca',
        'Impostazioni Form',
        'Impostazioni Form',
        'manage_options',
        'posti-barca-settings',
        'posti_barca_settings_page'
    );
}

// Pagina impostazioni
function posti_barca_settings_page() {
    if (isset($_POST['posti_barca_save_settings'])) {
        check_admin_referer('posti_barca_settings');
        $shortcode = wp_unslash($_POST['cf7_shortcode']);
        $shortcode = sanitize_text_field($shortcode);
        update_option('posti_barca_cf7_shortcode', $shortcode);
        echo '<div class="notice notice-success"><p>Impostazioni salvate!</p></div>';
    }
    
    $current_shortcode = get_option('posti_barca_cf7_shortcode', '');
    ?>
    <div class="wrap">
        <h1>Impostazioni Form Posti Barca</h1>
        <form method="post">
            <?php wp_nonce_field('posti_barca_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cf7_shortcode">Shortcode Contact Form 7</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="cf7_shortcode" 
                            name="cf7_shortcode" 
                            value="<?php echo esc_attr($current_shortcode); ?>" 
                            class="large-text"
                            placeholder="[contact-form-7 id=&quot;123&quot;]"
                        />
                        <p class="description">
                            Incolla qui lo shortcode del tuo Contact Form 7.<br>
                            <strong>Importante:</strong> Nel form CF7, usa <code>[text posto-barca readonly]</code> per il campo posto barca (visibile ma non modificabile)
                        </p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="posti_barca_save_settings" class="button button-primary" value="Salva Impostazioni">
            </p>
        </form>
    </div>
    <?php
}

// Registra il Custom Post Type
add_action('init', 'register_posti_barca_cpt');

function register_posti_barca_cpt() {

    $labels = array(
        'name'                  => 'Posti Barca',
        'singular_name'         => 'Posto Barca',
        'menu_name'             => 'Posti Barca',
        'name_admin_bar'        => 'Posto Barca',
        'add_new'               => 'Aggiungi nuovo',
        'add_new_item'          => 'Aggiungi nuovo Posto Barca',
        'new_item'              => 'Nuovo Posto Barca',
        'edit_item'             => 'Modifica Posto Barca',
        'view_item'             => 'Visualizza Posto Barca',
        'all_items'             => 'Tutti i Posti Barca',
        'search_items'          => 'Cerca Posti Barca',
        'not_found'             => 'Nessun Posto Barca trovato',
        'not_found_in_trash'    => 'Nessun Posto Barca nel cestino',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'posto-barca'),
        'menu_icon'          => 'dashicons-admin-multisite',
        'supports'           => array('title', 'editor', 'thumbnail'),
        'show_in_rest'       => true,
        'taxonomies'         => array('tipo_posto_barca', 'categoria_posto_barca'),
    );

    register_post_type('posto_barca', $args);
}

// Registra la tassonomia Tipo (obbligatoria: Affitto/Vendita)
add_action('init', 'register_tipo_posto_barca_taxonomy');

function register_tipo_posto_barca_taxonomy() {
    
    $labels = array(
        'name'              => 'Tipo',
        'singular_name'     => 'Tipo',
        'search_items'      => 'Cerca Tipo',
        'all_items'         => 'Tutti i Tipi',
        'edit_item'         => 'Modifica Tipo',
        'update_item'       => 'Aggiorna Tipo',
        'add_new_item'      => 'Aggiungi Nuovo Tipo',
        'new_item_name'     => 'Nuovo Nome Tipo',
        'menu_name'         => 'Tipo',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'tipo-posto-barca'),
        'show_in_rest'      => true,
    );

    register_taxonomy('tipo_posto_barca', array('posto_barca'), $args);
}

// Registra la tassonomia Categorie (creabili dall'utente)
add_action('init', 'register_categoria_posto_barca_taxonomy');

function register_categoria_posto_barca_taxonomy() {
    
    $labels = array(
        'name'              => 'Categorie',
        'singular_name'     => 'Categoria',
        'search_items'      => 'Cerca Categorie',
        'all_items'         => 'Tutte le Categorie',
        'parent_item'       => 'Categoria Genitore',
        'parent_item_colon' => 'Categoria Genitore:',
        'edit_item'         => 'Modifica Categoria',
        'update_item'       => 'Aggiorna Categoria',
        'add_new_item'      => 'Aggiungi Nuova Categoria',
        'new_item_name'     => 'Nuovo Nome Categoria',
        'menu_name'         => 'Categorie',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'categoria-posto-barca'),
        'show_in_rest'      => true,
    );

    register_taxonomy('categoria_posto_barca', array('posto_barca'), $args);
}

// Crea automaticamente le due categorie predefinite al momento dell'attivazione
register_activation_hook(__FILE__, 'posti_barca_activation');

function posti_barca_activation() {
    // Registra il CPT e la tassonomia
    register_posti_barca_cpt();
    register_tipo_posto_barca_taxonomy();
    
    // Crea le categorie se non esistono
    if (!term_exists('Affitto', 'tipo_posto_barca')) {
        wp_insert_term('Affitto', 'tipo_posto_barca', array(
            'slug' => 'affitto'
        ));
    }
    
    if (!term_exists('Vendita', 'tipo_posto_barca')) {
        wp_insert_term('Vendita', 'tipo_posto_barca', array(
            'slug' => 'vendita'
        ));
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Rendi la categoria obbligatoria
add_action('save_post_posto_barca', 'validate_posto_barca_tipo', 10, 2);

function validate_posto_barca_tipo($post_id, $post) {
    
    // Skip autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Skip auto-draft
    if ($post->post_status === 'auto-draft') {
        return;
    }
    
    // Skip se non è publish o future
    if (!in_array($post->post_status, array('publish', 'future'))) {
        return;
    }
    
    // Controlla se è assegnato un termine
    $terms = wp_get_post_terms($post_id, 'tipo_posto_barca');
    
    if (empty($terms) || is_wp_error($terms)) {
        // Se non c'è categoria, impedisci la pubblicazione
        remove_action('save_post_posto_barca', 'validate_posto_barca_tipo', 10);
        
        wp_update_post(array(
            'ID'          => $post_id,
            'post_status' => 'draft'
        ));
        
        add_action('save_post_posto_barca', 'validate_posto_barca_tipo', 10, 2);
        
        // Aggiungi un messaggio di errore personalizzato
        add_filter('redirect_post_location', function($location) {
            return add_query_arg('tipo_required', '1', $location);
        });
    }
}

// Validazione pre-salvataggio per l'editor a blocchi
add_filter('rest_pre_insert_posto_barca', 'validate_tipo_before_save', 10, 2);

function validate_tipo_before_save($prepared_post, $request) {
    // Permetti sempre il salvataggio per bozze e autosave
    $params = $request->get_params();
    
    // Permetti autosave e bozze
    if (isset($params['status']) && in_array($params['status'], array('draft', 'auto-draft'))) {
        return $prepared_post;
    }
    
    // Verifica se ci sono termini nella richiesta
    if (isset($params['tipo_posto_barca']) && !empty($params['tipo_posto_barca'])) {
        return $prepared_post;
    }
    
    // Controlla se il post ha già dei termini assegnati
    if (isset($prepared_post->ID) && $prepared_post->ID > 0) {
        $existing_terms = wp_get_post_terms($prepared_post->ID, 'tipo_posto_barca');
        
        if (!empty($existing_terms) && !is_wp_error($existing_terms)) {
            return $prepared_post;
        }
    }
    
    // Solo se sta cercando di pubblicare senza tipo, blocca
    if (isset($params['status']) && $params['status'] === 'publish') {
        return new WP_Error(
            'rest_cannot_edit',
            'Indicare obbligatoriamente se il posto barca è in affitto o in vendita.',
            array('status' => 403)
        );
    }
    
    return $prepared_post;
}

// Filtra il messaggio di errore per sostituire "Updating failed"
add_filter('wp_insert_post_data', 'filter_posto_barca_error_message', 99, 2);

function filter_posto_barca_error_message($data, $postarr) {
    if (isset($data['post_type']) && $data['post_type'] === 'posto_barca') {
        // Controlla se ha il tipo assegnato
        if (isset($postarr['ID']) && $postarr['ID'] > 0) {
            $terms = wp_get_post_terms($postarr['ID'], 'tipo_posto_barca');
            if (empty($terms) || is_wp_error($terms)) {
                // Forza lo stato a draft se non ha tipo
                $data['post_status'] = 'draft';
            }
        }
    }
    return $data;
}

// Mostra un avviso se la categoria non è stata selezionata
add_action('admin_notices', 'posto_barca_admin_notices');

function posto_barca_admin_notices() {
    if (isset($_GET['tipo_required']) && $_GET['tipo_required'] == '1') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>Errore:</strong> Indicare obbligatoriamente se il posto barca è in affitto o in vendita.</p>
        </div>
        <?php
    }
}

// La foto di copertina viene gestita tramite l'immagine in evidenza di WordPress
// Non è necessaria una galleria separata
