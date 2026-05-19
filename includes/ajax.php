<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_nopriv_wur_get_faqs', 'wur_ajax_get_faqs');
add_action('wp_ajax_wur_get_faqs', 'wur_ajax_get_faqs');
function wur_ajax_get_faqs() {
    check_ajax_referer('wur_nonce', 'nonce');
    $faqs = json_decode(get_option('wur_faqs', '[]'), true) ?: [];
    wp_send_json_success($faqs);
}
