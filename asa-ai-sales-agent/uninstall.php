<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

$asaaisaa_option_names = [
    'asaaisaa_api_key',
    'asaaisaa_system_prompt',
    'asaaisaa_title',
    'asaaisaa_subtitle',
    'asaaisaa_primary_color',
    'asaaisaa_avatar_icon',
    'asaaisaa_avatar_image_url',
    'asaaisaa_position',
    'asaaisaa_show_credit',
    'asaaisaa_auto_insert',
    'asaaisaa_display_types',
    'asaaisaa_history_limit',
    'asaaisaa_proactive_delay',
    'asaaisaa_cache_bust'
];

// Clear plugin options
foreach ( $asaaisaa_option_names as $asaaisaa_option ) {
	delete_option( $asaaisaa_option );
}

// Clear transients using a direct database query, as there is no other way.
global $wpdb;
$asaaisaa_transient_prefix_like = $wpdb->esc_like( '_transient_asaaisaa_proactive_message_' ) . '%';
$asaaisaa_timeout_prefix_like   = $wpdb->esc_like( '_transient_timeout_asaaisaa_proactive_message_' ) . '%';

// phpcs:disable WordPress.DB.DirectDatabaseQuery
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$asaaisaa_transient_prefix_like,
		$asaaisaa_timeout_prefix_like
	)
);
// phpcs:enable

// Flush the cache to ensure the transients are gone.
wp_cache_flush();

