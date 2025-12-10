<?php
if (!defined('ABSPATH')) exit;

/**
 * Restituisce il prezzo raw (numero) dello yacht.
 */
function yachts_get_price_raw($post_id = null) {
    $post_id = $post_id ?: get_the_ID();

    if (!$post_id) {
        return null;
    }

    $price = get_field('prezzo_yacht', $post_id);

    if ($price === null || $price === '') {
        return null;
    }

    return (float) $price;
}

/**
 * Restituisce il prezzo formattato (es. 100,00 €).
 * Configurazione centralizzata qui.
 */
function yachts_get_price_formatted($post_id = null, $decimals = 2, $currency = '€') {
    $price = yachts_get_price_raw($post_id);

    if ($price === null) {
        return '';
    }

    return esc_html( number_format_i18n($price, $decimals) ) . ' ' . $currency;
}

/**
 * Echo diretto (comodo nei template).
 */
function yachts_the_price($post_id = null, $decimals = 2, $currency = '€') {
    echo yachts_get_price_formatted($post_id, $decimals, $currency);
}
