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
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'yachts'),
        'menu_icon'          => 'dashicons-admin-post',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true, // utile anche per Gutenberg/API
    );

    register_post_type('yacht', $args);
}
