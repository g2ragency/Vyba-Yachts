<?php
/**
 * Plugin Name: Charter CPT
 * Description: Custom Post Type per Charter.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/charter-helpers.php';

add_action('init', 'register_charter_cpt');

function register_charter_cpt() {

    $labels = array(
        'name'                  => 'Charter',
        'singular_name'         => 'Charter',
        'menu_name'             => 'Charter',
        'name_admin_bar'        => 'Charter',
        'add_new'               => 'Aggiungi nuovo',
        'add_new_item'          => 'Aggiungi nuovo Charter',
        'new_item'              => 'Nuovo Charter',
        'edit_item'             => 'Modifica Charter',
        'view_item'             => 'Visualizza Charter',
        'all_items'             => 'Tutti i Charter',
        'search_items'          => 'Cerca Charter',
        'not_found'             => 'Nessun Charter trovato',
        'not_found_in_trash'    => 'Nessun Charter nel cestino',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'charter'),
        'menu_icon'          => 'dashicons-admin-post',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('charter', $args);
}

// Aggiungi metabox per galleria charter
add_action('add_meta_boxes', 'charter_gallery_metabox');

function charter_gallery_metabox() {
    add_meta_box(
        'charter_gallery',
        'Galleria Charter',
        'charter_gallery_metabox_callback',
        'charter',
        'normal',
        'high'
    );
    
    add_meta_box(
        'charter_scheda_tecnica',
        'Scheda Tecnica',
        'charter_scheda_tecnica_metabox_callback',
        'charter',
        'side',
        'default'
    );
}

function charter_gallery_metabox_callback($post) {
    wp_nonce_field('charter_gallery_nonce', 'charter_gallery_nonce');
    
    $gallery_ids = get_post_meta($post->ID, 'galleria_charter', true);
    $gallery_ids = $gallery_ids ? explode(',', $gallery_ids) : array();
    
    ?>
    <div class="charter-gallery-container">
        <div class="charter-gallery-images">
            <?php
            if (!empty($gallery_ids)) {
                foreach ($gallery_ids as $image_id) {
                    if ($image_id) {
                        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        echo '<div class="charter-gallery-image" data-id="' . esc_attr($image_id) . '">';
                        echo '<img src="' . esc_url($image_url) . '" />';
                        echo '<span class="remove-image">&times;</span>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        <input type="hidden" id="charter_gallery_ids" name="charter_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>" />
        <button type="button" class="button charter-gallery-add">Aggiungi Immagini</button>
    </div>
    
    <style>
        .charter-gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .charter-gallery-image {
            position: relative;
            width: 100px;
            height: 100px;
        }
        .charter-gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #ddd;
        }
        .charter-gallery-image .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3232;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        var frame;
        
        $('.charter-gallery-add').on('click', function(e) {
            e.preventDefault();
            
            if (frame) {
                frame.open();
                return;
            }
            
            frame = wp.media({
                title: 'Seleziona Immagini Galleria',
                button: { text: 'Aggiungi alla Galleria' },
                multiple: true
            });
            
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var ids = $('#charter_gallery_ids').val();
                var idsArray = ids ? ids.split(',') : [];
                
                selection.each(function(attachment) {
                    attachment = attachment.toJSON();
                    if (idsArray.indexOf(attachment.id.toString()) === -1) {
                        idsArray.push(attachment.id);
                        
                        var imageHtml = '<div class="charter-gallery-image" data-id="' + attachment.id + '">';
                        imageHtml += '<img src="' + attachment.sizes.thumbnail.url + '" />';
                        imageHtml += '<span class="remove-image">&times;</span>';
                        imageHtml += '</div>';
                        
                        $('.charter-gallery-images').append(imageHtml);
                    }
                });
                
                $('#charter_gallery_ids').val(idsArray.join(','));
            });
            
            frame.open();
        });
        
        $(document).on('click', '.charter-gallery-image .remove-image', function() {
            var imageId = $(this).parent().data('id');
            var ids = $('#charter_gallery_ids').val().split(',');
            ids = ids.filter(function(id) { return id != imageId; });
            $('#charter_gallery_ids').val(ids.join(','));
            $(this).parent().remove();
        });
    });
    </script>
    <?php
}

// Metabox Scheda Tecnica
function charter_scheda_tecnica_metabox_callback($post) {
    wp_nonce_field('charter_scheda_tecnica_nonce', 'charter_scheda_tecnica_nonce');
    
    $scheda_tecnica_id = get_post_meta($post->ID, 'scheda_tecnica_charter', true);
    $file_url = '';
    $file_name = '';
    
    if ($scheda_tecnica_id) {
        $file_url = wp_get_attachment_url($scheda_tecnica_id);
        $file_name = basename($file_url);
    }
    ?>
    <div class="charter-scheda-tecnica-container">
        <div class="charter-scheda-file">
            <?php if ($file_url) : ?>
                <div class="scheda-file-preview">
                    <span class="dashicons dashicons-pdf"></span>
                    <a href="<?php echo esc_url($file_url); ?>" target="_blank">
                        <?php echo esc_html($file_name); ?>
                    </a>
                    <button type="button" class="button-link charter-scheda-remove" style="color: #dc3232; margin-left: 10px;">
                        Rimuovi
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <input type="hidden" id="charter_scheda_tecnica_id" name="charter_scheda_tecnica_id" value="<?php echo esc_attr($scheda_tecnica_id); ?>" />
        <button type="button" class="button charter-scheda-upload" style="margin-top: 10px;">
            <?php echo $file_url ? 'Cambia PDF' : 'Carica PDF'; ?>
        </button>
    </div>
    
    <style>
        .scheda-file-preview {
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .scheda-file-preview .dashicons {
            color: #dc3232;
            font-size: 24px;
            width: 24px;
            height: 24px;
        }
        .scheda-file-preview a {
            flex: 1;
            text-decoration: none;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        var frame;
        
        $('.charter-scheda-upload').on('click', function(e) {
            e.preventDefault();
            
            if (frame) {
                frame.open();
                return;
            }
            
            frame = wp.media({
                title: 'Seleziona PDF Scheda Tecnica',
                button: { text: 'Usa questo file' },
                multiple: false,
                library: { type: 'application/pdf' }
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                
                $('#charter_scheda_tecnica_id').val(attachment.id);
                
                var html = '<div class="scheda-file-preview">';
                html += '<span class="dashicons dashicons-pdf"></span>';
                html += '<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>';
                html += '<button type="button" class="button-link charter-scheda-remove" style="color: #dc3232; margin-left: 10px;">Rimuovi</button>';
                html += '</div>';
                
                $('.charter-scheda-file').html(html);
                $('.charter-scheda-upload').text('Cambia PDF');
            });
            
            frame.open();
        });
        
        $(document).on('click', '.charter-scheda-remove', function(e) {
            e.preventDefault();
            $('#charter_scheda_tecnica_id').val('');
            $('.charter-scheda-file').empty();
            $('.charter-scheda-upload').text('Carica PDF');
        });
    });
    </script>
    <?php
}

add_action('save_post', 'charter_gallery_save_metabox');

function charter_gallery_save_metabox($post_id) {
    if (!isset($_POST['charter_gallery_nonce']) || !wp_verify_nonce($_POST['charter_gallery_nonce'], 'charter_gallery_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['charter_gallery_ids'])) {
        update_post_meta($post_id, 'galleria_charter', sanitize_text_field($_POST['charter_gallery_ids']));
    }
}

add_action('save_post', 'charter_scheda_tecnica_save_metabox');

function charter_scheda_tecnica_save_metabox($post_id) {
    if (!isset($_POST['charter_scheda_tecnica_nonce']) || !wp_verify_nonce($_POST['charter_scheda_tecnica_nonce'], 'charter_scheda_tecnica_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['charter_scheda_tecnica_id'])) {
        $scheda_id = absint($_POST['charter_scheda_tecnica_id']);
        if ($scheda_id) {
            update_post_meta($post_id, 'scheda_tecnica_charter', $scheda_id);
        } else {
            delete_post_meta($post_id, 'scheda_tecnica_charter');
        }
    }
}

// Flush rewrite rules on plugin activation
register_activation_hook(__FILE__, 'charter_cpt_activation');

function charter_cpt_activation() {
    register_charter_cpt();
    flush_rewrite_rules();
}
