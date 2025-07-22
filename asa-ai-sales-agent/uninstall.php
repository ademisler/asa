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

// Clear plugin options
foreach ( $option_names as $option ) {
	delete_option( $option );
}

// Clear transients using a direct database query, as there is no other way.
global $wpdb;
$transient_prefix_like = $wpdb->esc_like( '_transient_asa_proactive_message_' ) . '%';
$timeout_prefix_like   = $wpdb->esc_like( '_transient_timeout_asa_proactive_message_' ) . '%';

// phpcs:disable WordPress.DB.DirectDatabaseQuery
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$transient_prefix_like,
		$timeout_prefix_like
	)
);
// phpcs:enable

// Flush the cache to ensure the transients are gone.
wp_cache_flush();

