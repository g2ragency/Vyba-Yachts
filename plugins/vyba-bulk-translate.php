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
        $results = array_merge($results, vyba_run_bulk_translate_taxonomies());
    }

    // Cleanup duplicates
    if (isset($_POST['vyba_cleanup_tax']) && check_admin_referer('vyba_bulk_translate_nonce')) {
        $results = vyba_cleanup_duplicate_terms();
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

        <form method="post" style="margin-top: 10px;">
            <?php wp_nonce_field('vyba_bulk_translate_nonce'); ?>
            <p><strong>Pulizia tassonomie:</strong> Elimina i termini duplicati (-it, -en con 0 post), mantiene gli originali (con post), imposta la lingua e crea le traduzioni EN corrette.</p>
            <input type="submit" name="vyba_cleanup_tax" class="button button-secondary" value="Pulisci e ricollega tassonomie" onclick="return confirm('Sicuro? Eliminerà i duplicati con 0 post e ricreerà i collegamenti.');">
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

/**
 * Translate taxonomy terms: create EN versions and link them via Polylang.
 */
function vyba_run_bulk_translate_taxonomies() {
    $results = [];

    // Map of taxonomy => [ IT slug => EN name ]
    $translations = [
        'tipo_posto_barca' => [
            'affitto'  => 'Rent',
            'vendita'  => 'Sale',
        ],
        'categoria_posto_barca' => [
            // Auto-translate common ones, rest get copied as-is
        ],
    ];

    $taxonomies = ['tipo_posto_barca', 'categoria_posto_barca'];

    foreach ($taxonomies as $tax) {
        if (!taxonomy_exists($tax)) continue;

        $terms = get_terms([
            'taxonomy'   => $tax,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || empty($terms)) continue;

        $created = 0;
        $linked = 0;
        $skipped = 0;

        foreach ($terms as $term) {
            // Set language to IT if not set
            $lang = pll_get_term_language($term->term_id);
            if (!$lang) {
                pll_set_term_language($term->term_id, 'it');
                $lang = 'it';
            }

            // Only process IT terms
            if ($lang !== 'it') {
                $skipped++;
                continue;
            }

            // Check if EN translation already linked
            $en_term_id = pll_get_term($term->term_id, 'en');
            if ($en_term_id) {
                $skipped++;
                continue;
            }

            // Determine EN name
            $en_name = $term->name; // default: keep same
            if (isset($translations[$tax][$term->slug])) {
                $en_name = $translations[$tax][$term->slug];
            }

            // Check if an unlinked EN term with same slug already exists
            $existing = get_terms([
                'taxonomy'   => $tax,
                'hide_empty' => false,
                'slug'       => $term->slug,
            ]);

            $found_en = null;
            if (!is_wp_error($existing)) {
                foreach ($existing as $ex) {
                    if ($ex->term_id === $term->term_id) continue;
                    $ex_lang = pll_get_term_language($ex->term_id);
                    if ($ex_lang === 'en' || $ex_lang === '') {
                        $found_en = $ex;
                        break;
                    }
                }
            }

            // Also search by name if not found by slug
            if (!$found_en) {
                $existing_by_name = get_terms([
                    'taxonomy'   => $tax,
                    'hide_empty' => false,
                    'name'       => $term->name,
                ]);
                if (!is_wp_error($existing_by_name)) {
                    foreach ($existing_by_name as $ex) {
                        if ($ex->term_id === $term->term_id) continue;
                        $ex_lang = pll_get_term_language($ex->term_id);
                        if ($ex_lang === 'en' || $ex_lang === '') {
                            $found_en = $ex;
                            break;
                        }
                    }
                }
            }

            if ($found_en) {
                // Link existing duplicate as EN translation
                pll_set_term_language($found_en->term_id, 'en');
                // Update name if we have a translation
                if ($en_name !== $term->name) {
                    wp_update_term($found_en->term_id, $tax, ['name' => $en_name, 'slug' => $term->slug . '-en']);
                }
                pll_save_term_translations([
                    'it' => $term->term_id,
                    'en' => $found_en->term_id,
                ]);
                $linked++;
            } else {
                // Create new EN term
                $en_slug = $term->slug . '-en';
                $new_term = wp_insert_term($en_name, $tax, [
                    'slug'        => $en_slug,
                    'description' => $term->description,
                    'parent'      => $term->parent,
                ]);

                if (is_wp_error($new_term)) {
                    $results[] = ['type' => 'error', 'text' => "Errore creando termine EN \"{$en_name}\" in {$tax}: " . $new_term->get_error_message()];
                    continue;
                }

                $new_term_id = $new_term['term_id'];
                pll_set_term_language($new_term_id, 'en');
                pll_save_term_translations([
                    'it' => $term->term_id,
                    'en' => $new_term_id,
                ]);
                $created++;
            }
        }

        $results[] = ['type' => 'success', 'text' => "Tassonomia {$tax}: {$created} termini EN creati, {$linked} duplicati collegati, {$skipped} saltati."];
    }

    return $results;
}

/**
 * Cleanup duplicate taxonomy terms created by the script.
 * 1. Find original terms (slug without -it/-en suffix, usually have posts)
 * 2. Delete duplicates with -it and -en suffixes
 * 3. Set original as IT
 * 4. Create a clean EN translation linked to the original
 */
function vyba_cleanup_duplicate_terms() {
    $results = [];
    $taxonomies = ['tipo_posto_barca', 'categoria_posto_barca'];

    $translations_map = [
        'affitto'  => 'Rent',
        'vendita'  => 'Sale',
    ];

    foreach ($taxonomies as $tax) {
        if (!taxonomy_exists($tax)) continue;

        $all_terms = get_terms([
            'taxonomy'   => $tax,
            'hide_empty' => false,
        ]);

        if (is_wp_error($all_terms) || empty($all_terms)) continue;

        // Group terms by base slug (remove -it/-en suffix)
        $groups = [];
        foreach ($all_terms as $term) {
            $base_slug = preg_replace('/-(?:it|en)$/', '', $term->slug);
            $groups[$base_slug][] = $term;
        }

        $deleted = 0;
        $fixed = 0;

        foreach ($groups as $base_slug => $terms_in_group) {
            if (count($terms_in_group) <= 1) {
                // Single term, just make sure it has IT language
                $term = $terms_in_group[0];
                $lang = pll_get_term_language($term->term_id);
                if (!$lang) {
                    pll_set_term_language($term->term_id, 'it');
                }
                continue;
            }

            // Find the original (the one with matching base slug, or with most posts)
            $original = null;
            $duplicates = [];

            foreach ($terms_in_group as $term) {
                if ($term->slug === $base_slug) {
                    $original = $term;
                } else {
                    $duplicates[] = $term;
                }
            }

            // If no exact base slug match, pick the one with most posts
            if (!$original) {
                usort($terms_in_group, function($a, $b) { return $b->count - $a->count; });
                $original = $terms_in_group[0];
                $duplicates = array_slice($terms_in_group, 1);
            }

            // Reassign posts from duplicates to original before deleting
            foreach ($duplicates as $dup) {
                $posts_with_dup = get_posts([
                    'post_type' => 'posto_barca',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'tax_query' => [
                        ['taxonomy' => $tax, 'terms' => $dup->term_id],
                    ],
                ]);
                foreach ($posts_with_dup as $pid) {
                    wp_set_object_terms($pid, [$original->term_id], $tax, true);
                }
                wp_delete_term($dup->term_id, $tax);
                $deleted++;
            }

            // Set original as IT
            pll_set_term_language($original->term_id, 'it');

            // Check if EN translation already exists
            $existing_en = pll_get_term($original->term_id, 'en');
            if (!$existing_en) {
                $en_name = isset($translations_map[$original->slug]) ? $translations_map[$original->slug] : $original->name;
                $en_slug = $original->slug . '-en';

                $new_term = wp_insert_term($en_name, $tax, [
                    'slug'        => $en_slug,
                    'description' => $original->description,
                    'parent'      => $original->parent,
                ]);

                if (!is_wp_error($new_term)) {
                    pll_set_term_language($new_term['term_id'], 'en');
                    pll_save_term_translations([
                        'it' => $original->term_id,
                        'en' => $new_term['term_id'],
                    ]);
                    $fixed++;
                } else {
                    $results[] = ['type' => 'error', 'text' => "Errore creando EN per \"{$original->name}\": " . $new_term->get_error_message()];
                }
            }
        }

        $results[] = ['type' => 'success', 'text' => "Tassonomia {$tax}: {$deleted} duplicati eliminati, {$fixed} traduzioni EN ricreate correttamente."];
    }

    return $results;
}
