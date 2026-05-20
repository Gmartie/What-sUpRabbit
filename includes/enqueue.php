<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'wur_enqueue_public');
function wur_enqueue_public() {
    wp_enqueue_style('wur-public', WUR_URL . 'public/css/wur-public.css', [], WUR_VERSION);
    wp_enqueue_script('wur-public', WUR_URL . 'public/js/wur-public.js', [], WUR_VERSION, true);

    $faqs_raw = get_option('wur_faqs', '[]');
    $faqs     = json_decode($faqs_raw, true) ?: [];

    wp_localize_script('wur-public', 'wurData', [
        'whatsappNumber'  => get_option('wur_whatsapp_number', ''),
        'welcomeMessage'  => get_option('wur_welcome_message', '¡Hola! ¿En qué podemos ayudarte?'),
        'buttonText'      => get_option('wur_button_text', 'Chatea con nosotros'),
        'buttonColor'     => get_option('wur_button_color', '#25d366'),
        'buttonTextColor' => get_option('wur_button_text_color', '#ffffff'),
        'headerColor'     => get_option('wur_header_color', '#2d8a4e'),
        'headerTextColor' => get_option('wur_header_text_color', '#ffffff'),
        'bubbleColor'     => get_option('wur_bubble_color', '#2d8a4e'),
        'bubbleTextColor' => get_option('wur_bubble_text_color', '#ffffff'),
        'bubbleIconUrl'   => get_option('wur_bubble_icon_url', ''),
        'bubbleMode'      => get_option('wur_bubble_mode', 'logo'),
        'bubbleBgColor'   => get_option('wur_bubble_bg_color', '#25d366'),
        'buttonStyle'     => get_option('wur_button_style', ''),
        'position'        => get_option('wur_position', 'right'),
        'companyName'     => get_option('wur_company_name', 'Nuestra empresa'),
        'offlineMessage'  => get_option('wur_offline_message', 'Estamos offline en este momento. Te responderemos pronto.'),
        'faqs'            => $faqs,
        'logoUrl'         => WUR_URL . 'WURabbitLogo.svg',
        'ajaxUrl'         => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('wur_nonce'),
    ]);
}

add_action('admin_enqueue_scripts', 'wur_enqueue_admin');
function wur_enqueue_admin($hook) {
    if ($hook !== 'toplevel_page_whatsup-rabbit') return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('wur-admin', WUR_URL . 'public/css/wur-admin.css', [], WUR_VERSION);
    wp_enqueue_script('wur-admin', WUR_URL . 'public/js/wur-admin.js', ['jquery', 'wp-color-picker'], WUR_VERSION, true);
    wp_enqueue_media();
}
