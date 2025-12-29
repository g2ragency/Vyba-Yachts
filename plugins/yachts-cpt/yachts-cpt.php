<?php
/**
 * Plugin Name: Yachts CPT
 * Description: Custom Post Type per Yachts.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/yachts-helpers.php';

add_action('init', 'register_yachts_cpt');

function register_yachts_cpt() {

    $labels = array(
        'name'                  => 'Yachts',
        'singular_name'         => 'Yacht',
        'menu_name'             => 'Yachts',
        'name_admin_bar'        => 'Yacht',
        'add_new'               => 'Aggiungi nuovo',
        'add_new_item'          => 'Aggiungi nuovo Yacht',
        'new_item'              => 'Nuovo Yacht',
        'edit_item'             => 'Modifica Yacht',
        'view_item'             => 'Visualizza Yacht',
        'all_items'             => 'Tutti gli Yachts',
        'search_items'          => 'Cerca Yachts',
        'not_found'             => 'Nessuno Yacht trovato',
        'not_found_in_trash'    => 'Nessuno Yacht nel cestino',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'yacht'),
        'menu_icon'          => 'dashicons-admin-post',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true, // utile anche per Gutenberg/API
    );

    register_post_type('yacht', $args);
}

// Aggiungi metabox per galleria yacht
add_action('add_meta_boxes', 'yacht_gallery_metabox');

function yacht_gallery_metabox() {
    add_meta_box(
        'yacht_gallery',
        'Galleria Yacht',
        'yacht_gallery_metabox_callback',
        'yacht',
        'normal',
        'high'
    );
}

function yacht_gallery_metabox_callback($post) {
    wp_nonce_field('yacht_gallery_nonce', 'yacht_gallery_nonce');
    
    $gallery_ids = get_post_meta($post->ID, 'galleria_yacht', true);
    $gallery_ids = $gallery_ids ? explode(',', $gallery_ids) : array();
    
    ?>
    <div class="yacht-gallery-container">
        <div class="yacht-gallery-images">
            <?php
            if (!empty($gallery_ids)) {
                foreach ($gallery_ids as $image_id) {
                    if ($image_id) {
                        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        echo '<div class="yacht-gallery-image" data-id="' . esc_attr($image_id) . '">';
                        echo '<img src="' . esc_url($image_url) . '" />';
                        echo '<span class="remove-image">&times;</span>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        <input type="hidden" id="yacht_gallery_ids" name="yacht_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>" />
        <button type="button" class="button yacht-gallery-add">Aggiungi Immagini</button>
    </div>
    
    <style>
        .yacht-gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .yacht-gallery-image {
            position: relative;
            width: 100px;
            height: 100px;
        }
        .yacht-gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #ddd;
        }
        .yacht-gallery-image .remove-image {
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
        
        $('.yacht-gallery-add').on('click', function(e) {
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
                var ids = $('#yacht_gallery_ids').val();
                var idsArray = ids ? ids.split(',') : [];
                
                selection.each(function(attachment) {
                    attachment = attachment.toJSON();
                    if (idsArray.indexOf(attachment.id.toString()) === -1) {
                        idsArray.push(attachment.id);
                        
                        var imageHtml = '<div class="yacht-gallery-image" data-id="' + attachment.id + '">';
                        imageHtml += '<img src="' + attachment.sizes.thumbnail.url + '" />';
                        imageHtml += '<span class="remove-image">&times;</span>';
                        imageHtml += '</div>';
                        
                        $('.yacht-gallery-images').append(imageHtml);
                    }
                });
                
                $('#yacht_gallery_ids').val(idsArray.join(','));
            });
            
            frame.open();
        });
        
        $(document).on('click', '.yacht-gallery-image .remove-image', function() {
            var imageId = $(this).parent().data('id');
            var ids = $('#yacht_gallery_ids').val().split(',');
            ids = ids.filter(function(id) { return id != imageId; });
            $('#yacht_gallery_ids').val(ids.join(','));
            $(this).parent().remove();
        });
    });
    </script>
    <?php
}

add_action('save_post', 'yacht_gallery_save_metabox');

function yacht_gallery_save_metabox($post_id) {
    if (!isset($_POST['yacht_gallery_nonce']) || !wp_verify_nonce($_POST['yacht_gallery_nonce'], 'yacht_gallery_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['yacht_gallery_ids'])) {
        update_post_meta($post_id, 'galleria_yacht', sanitize_text_field($_POST['yacht_gallery_ids']));
    }
}

// Flush rewrite rules on plugin activation
register_activation_hook(__FILE__, 'yachts_cpt_activation');

function yachts_cpt_activation() {
    register_yachts_cpt();
    flush_rewrite_rules();
}
