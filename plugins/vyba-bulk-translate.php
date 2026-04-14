<?php
/**
 * Plugin Name: Vyba Bulk Translate (One-Shot)
 * Description: Duplica tutti i post IT dei CPT (yacht, charter, posto_barca) come traduzioni EN in Polylang. Eseguilo una volta e poi disattivalo/cancellalo.
 * Version: 1.0
 * Author: Vyba Dev
 */

if (!defined('ABSPATH')) exit;

// Admin page
add_action('admin_menu', function() {
    add_management_page(
        'Vyba Bulk Translate',
        'Vyba Bulk Translate',
        'manage_options',
        'vyba-bulk-translate',
        'vyba_bulk_translate_page'
    );
});

function vyba_bulk_translate_page() {
    if (!current_user_can('manage_options')) return;

    // Check Polylang is active
    if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
        echo '<div class="wrap"><h1>Vyba Bulk Translate</h1>';
        echo '<div class="notice notice-error"><p>Polylang non è attivo o non supporta i CPT. Attiva Polylang e abilita i CPT nelle impostazioni.</p></div></div>';
        return;
    }

    $post_types = ['yacht', 'charter', 'posto_barca'];
    $results = [];

    // Execute if form submitted
    if (isset($_POST['vyba_run_bulk']) && check_admin_referer('vyba_bulk_translate_nonce')) {
        $results = vyba_run_bulk_translate($post_types);
    }

    // Count existing posts
    $counts = [];
    foreach ($post_types as $pt) {
        $all = get_posts([
            'post_type' => $pt,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        $need_translation = 0;
        foreach ($all as $post_id) {
            $lang = pll_get_post_language($post_id);
            if ($lang === 'it' || $lang === '') {
                $en_id = pll_get_post($post_id, 'en');
                if (!$en_id) {
                    $need_translation++;
                }
            }
        }
        $counts[$pt] = ['total' => count($all), 'need' => $need_translation];
    }

    ?>
    <div class="wrap">
        <h1>Vyba Bulk Translate</h1>
        <p>Questo tool duplica tutti i post IT dei CPT come traduzioni EN in Polylang, copiando titolo, contenuto, excerpt, thumbnail e tutti i meta/ACF fields.</p>

        <h2>Situazione attuale</h2>
        <table class="widefat" style="max-width: 500px;">
            <thead>
                <tr><th>Post Type</th><th>Totale</th><th>Da tradurre</th></tr>
            </thead>
            <tbody>
                <?php foreach ($counts as $pt => $c) : ?>
                <tr>
                    <td><strong><?php echo esc_html($pt); ?></strong></td>
                    <td><?php echo $c['total']; ?></td>
                    <td><?php echo $c['need']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($results)) : ?>
            <h2>Risultati</h2>
            <?php foreach ($results as $msg) : ?>
                <div class="notice notice-<?php echo $msg['type']; ?>"><p><?php echo esc_html($msg['text']); ?></p></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="post" style="margin-top: 20px;">
            <?php wp_nonce_field('vyba_bulk_translate_nonce'); ?>
            <p><strong>Attenzione:</strong> Questa operazione crea i post EN come copia esatta degli IT. Potrai poi tradurre i contenuti dall'editor. I campi numerici (prezzo, cabine, ecc.) restano identici.</p>
            <input type="submit" name="vyba_run_bulk" class="button button-primary" value="Crea tutte le traduzioni EN" onclick="return confirm('Sicuro? Verranno creati i post EN per tutti i CPT.');">
        </form>

        <p style="margin-top: 30px; color: #888;"><em>Dopo l'uso, disattiva ed elimina questo plugin.</em></p>
    </div>
    <?php
}

function vyba_run_bulk_translate($post_types) {
    $results = [];

    foreach ($post_types as $pt) {
        $posts = get_posts([
            'post_type' => $pt,
            'post_status' => 'any',
            'posts_per_page' => -1,
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($posts as $post) {
            $post_id = $post->ID;

            // Ensure the original is set as Italian
            $lang = pll_get_post_language($post_id);
            if (!$lang) {
                pll_set_post_language($post_id, 'it');
            } elseif ($lang !== 'it') {
                $skipped++;
                continue; // Skip non-IT posts
            }

            // Check if EN translation already exists
            $en_id = pll_get_post($post_id, 'en');
            if ($en_id) {
                $skipped++;
                continue;
            }

            // Create the EN duplicate
            $new_post = [
                'post_type'    => $post->post_type,
                'post_title'   => $post->post_title,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_status'  => $post->post_status,
                'post_author'  => $post->post_author,
                'post_parent'  => $post->post_parent,
                'menu_order'   => $post->menu_order,
                'comment_status' => $post->comment_status,
                'ping_status'  => $post->ping_status,
            ];

            $new_id = wp_insert_post($new_post);

            if (is_wp_error($new_id)) {
                $results[] = ['type' => 'error', 'text' => "Errore creando traduzione per \"{$post->post_title}\" (ID {$post_id}): " . $new_id->get_error_message()];
                continue;
            }

            // Copy all post meta
            $all_meta = get_post_meta($post_id);
            if ($all_meta) {
                foreach ($all_meta as $key => $values) {
                    // Skip internal WP/Polylang meta
                    if (in_array($key, ['_edit_lock', '_edit_last', '_wp_old_slug'])) continue;

                    delete_post_meta($new_id, $key);
                    foreach ($values as $value) {
                        add_post_meta($new_id, $key, maybe_unserialize($value));
                    }
                }
            }

            // Copy thumbnail
            $thumb_id = get_post_thumbnail_id($post_id);
            if ($thumb_id) {
                set_post_thumbnail($new_id, $thumb_id);
            }

            // Copy taxonomy terms (for posto_barca)
            $taxonomies = get_object_taxonomies($post->post_type);
            foreach ($taxonomies as $tax) {
                if ($tax === 'language' || $tax === 'post_translations') continue;
                $terms = wp_get_object_terms($post_id, $tax, ['fields' => 'ids']);
                if (!empty($terms) && !is_wp_error($terms)) {
                    wp_set_object_terms($new_id, $terms, $tax);
                }
            }

            // Set language and link translations
            pll_set_post_language($new_id, 'en');
            pll_save_post_translations([
                'it' => $post_id,
                'en' => $new_id,
            ]);

            $created++;
        }

        $results[] = ['type' => 'success', 'text' => "{$pt}: {$created} traduzioni EN create, {$skipped} saltati (già tradotti o non IT)."];
    }

    return $results;
}
