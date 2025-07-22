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
    'asa_show_credit',
    'asa_auto_insert',
    'asa_display_types',
    'asa_history_limit',
    'asa_proactive_delay'
];

foreach ($option_names as $option) {
    delete_option($option);
}

global $wpdb;
$like       = $wpdb->esc_like( 'asa_proactive_message_' ) . '%';
$cache_key  = 'asa_uninstall_transients';
$rows       = wp_cache_get( $cache_key );
if ( false === $rows ) {
    $rows = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_' . $like,
            '_transient_timeout_' . $like
        )
    );
    wp_cache_set( $cache_key, $rows );
}
if ($rows) {
    $transients = array_unique(array_map(function ($name) {
        return str_replace(['_transient_', '_transient_timeout_'], '', $name);
    }, $rows));
    foreach ($transients as $transient) {
        delete_transient($transient);
    }
}

