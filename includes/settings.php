<?php
if (!defined('ABSPATH')) exit;

add_action('admin_init', 'wur_register_settings');
function wur_register_settings() {
    $options = [
        'wur_whatsapp_number',
        'wur_welcome_message',
        'wur_button_text',
        'wur_button_color',
        'wur_button_text_color',
        'wur_header_color',
        'wur_header_text_color',
        'wur_bubble_color',
        'wur_bubble_text_color',
        'wur_bubble_icon_url',
        'wur_position',
        'wur_faqs',
        'wur_company_name',
        'wur_offline_message',
        'wur_show_assistant',
        'wur_assistant_greeting',
    ];
    foreach ($options as $opt) {
        register_setting('wur_settings_group', $opt);
    }
}
