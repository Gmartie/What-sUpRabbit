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
        'wur_bubble_mode',
        'wur_bubble_bg_color',
        'wur_button_style',
        'wur_header_icon_url',
        'wur_header_icon_mode',
        'wur_header_icon_color',
        'wur_msg_text_color',
        // NEW: horario, agentes, notificacion
        'wur_schedule_enabled',
        'wur_schedule_days',
        'wur_schedule_start',
        'wur_schedule_end',
        'wur_agents',
        'wur_notification_timeout',
    ];
    foreach ($options as $opt) {
        register_setting('wur_settings_group', $opt);
    }
}

/* Sanitize wur_button_style array → string */
add_filter('pre_update_option_wur_button_style', function ($new_value) {
    if (is_array($new_value)) {
        $allowed = ['bold', 'italic', 'underline'];
        $new_value = implode(',', array_intersect($new_value, $allowed));
    }
    return sanitize_text_field($new_value);
});

/* Sanitize wur_schedule_days array → JSON */
add_filter('pre_update_option_wur_schedule_days', function ($new_value) {
    if (is_array($new_value)) {
        $allowed = ['mon','tue','wed','thu','fri','sat','sun'];
        $new_value = json_encode(array_values(array_intersect($new_value, $allowed)));
    }
    return $new_value;
});