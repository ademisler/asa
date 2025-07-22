<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

$option_names = [
    'asa_api_key',
    'asa_system_prompt',
    'asa_title',
    'asa_subtitle',
    'asa_primary_color',
    'asa_avatar_icon',
    'asa_avatar_image_url',
    'asa_position',
    'asa_show_credit'
];

foreach ($option_names as $option) {
    delete_option($option);
}

global $wpdb;
$like = $wpdb->esc_like('asa_proactive_message_');
$query = "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_{$like}%' OR option_name LIKE '_transient_timeout_{$like}%'";
$rows = $wpdb->get_col($query);
if ($rows) {
    $transients = array_unique(array_map(function ($name) {
        return str_replace(['_transient_', '_transient_timeout_'], '', $name);
    }, $rows));
    foreach ($transients as $transient) {
        delete_transient($transient);
    }
}

