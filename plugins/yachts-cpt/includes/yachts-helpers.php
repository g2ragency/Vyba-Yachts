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
 * Restituisce il prezzo formattato (es. 295.000 €).
 * Configurazione centralizzata qui.
 */
function yachts_get_price_formatted($post_id = null, $decimals = 0, $currency = '€') {
    $price = yachts_get_price_raw($post_id);

    if ($price === null) {
        return '';
    }

    // Formatta con punto come separatore delle migliaia e senza decimali
    return number_format($price, $decimals, ',', '.') . ' ' . $currency;
}

/**
 * Echo diretto (comodo nei template).
 */
function yachts_the_price($post_id = null, $decimals = 0, $currency = '€') {
    echo yachts_get_price_formatted($post_id, $decimals, $currency);
}
