<?php
/**
 * Plugin Name: Ai Sales Agent (ASA)
 * Plugin URI: https://wordpress.org/plugins/asa-ai-sales-agent/
 * Description: Transform your website into a sales powerhouse with an intelligent AI chatbot powered by Google Gemini that proactively engages visitors, analyzes page content, and drives conversions through contextual conversations.
 * Version: 1.0.7
 * Author: Adem Isler
 * Author URI: https://ademisler.com/en
 * Text Domain: asa-ai-sales-agent
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * @package ASA_AI_Sales_Agent
 * @author Adem Isler
 * @copyright 2025 Adem Isler
 * @license GPL-2.0-or-later
 * 
 * Ai Sales Agent (ASA) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * Ai Sales Agent (ASA) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Ai Sales Agent (ASA). If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 * 
 * PRIVACY NOTICE: This plugin sends user messages and page content to Google's Gemini API
 * for processing. Please review Google's privacy policy and ensure compliance with
 * applicable privacy regulations in your jurisdiction.
 */

// Define plugin version constant for cache busting and version checks
define('ASA_VERSION', '1.0.7');

// Prevent direct access to this file for security
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Main Ai Sales Agent (ASA) Plugin Class
 * 
 * This class handles all plugin functionality including:
 * - Settings management and admin interface
 * - Frontend chatbot rendering and functionality
 * - Google Gemini API integration
 * - AJAX handlers for chat and proactive messaging
 * - Asset enqueuing and localization
 * 
 * @since 1.0.0
 * @package ASA_AI_Sales_Agent
 */
class ASAAISalesAgent {
    
    /**
     * Single instance of the plugin class
     * 
     * @var ASAAISalesAgent|null
     * @since 1.0.0
     */
    private static $instance = null;

    /**
     * Get single instance of the plugin class (Singleton pattern)
     * 
     * Ensures only one instance of the plugin class exists throughout
     * the WordPress request lifecycle.
     * 
     * @return ASAAISalesAgent The single instance of the plugin
     * @since 1.0.0
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     * 
     * Initializes all WordPress hooks and actions needed for the plugin to function.
     * This includes admin interface, frontend functionality, AJAX handlers, and
     * shortcode registration.
     * 
     * @since 1.0.0
     */
    private function __construct() {
        // Admin interface hooks
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Asset enqueuing hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Frontend functionality
        add_shortcode('asa_chatbot', array($this, 'render_chatbot'));
        add_action('wp_footer', array($this, 'maybe_print_chatbot'));
        
        // AJAX handlers for chat functionality (both logged in and logged out users)
        add_action('wp_ajax_asa_chat', array($this, 'handle_chat_request'));
        add_action('wp_ajax_nopriv_asa_chat', array($this, 'handle_chat_request'));
        
        // AJAX handlers for proactive messaging (both logged in and logged out users)
        add_action('wp_ajax_asa_generate_proactive_message', array($this, 'handle_proactive_message_request'));
        add_action('wp_ajax_nopriv_asa_generate_proactive_message', array($this, 'handle_proactive_message_request'));
        
        // AJAX handlers for admin functionality (logged in users only)
        add_action('wp_ajax_asa_save_settings', array($this, 'asa_save_settings'));
        add_action('wp_ajax_asa_test_api_key', array($this, 'asa_test_api_key'));
    }

    /**
     * Enqueue frontend assets (CSS and JavaScript)
     * 
     * Loads all necessary stylesheets and scripts for the frontend chatbot functionality.
     * This includes:
     * - Main chatbot styles and FontAwesome icons
     * - Google Fonts for typography
     * - Third-party libraries (Showdown for Markdown, DOMPurify for sanitization)
     * - Main chatbot JavaScript with localized settings
     * 
     * @since 1.0.0
     * @hook wp_enqueue_scripts
     */
    public function enqueue_assets() {
        // Enqueue stylesheets with cache busting
        wp_enqueue_style('asa-style', plugins_url('css/asa-style.css', __FILE__), [], ASA_VERSION . '-' . get_option('asa_cache_bust', time()));
        wp_enqueue_style('asa-fa', plugins_url('assets/css/all.min.css', __FILE__), [], ASA_VERSION);
        
        // Load Google Fonts for better typography (with display=swap for performance)
        wp_enqueue_style('asa-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', [], null);
        
        // Enqueue third-party JavaScript libraries
        wp_enqueue_script('showdown', plugins_url('assets/js/showdown.min.js', __FILE__), [], '2.1.0', true);
        wp_enqueue_script('dompurify', plugins_url('assets/js/dompurify.min.js', __FILE__), [], '2.4.1', true);
        
        // Enqueue main chatbot script with dependencies and cache busting
        wp_enqueue_script('asa-script', plugins_url('js/asa-script.js', __FILE__), array('jquery', 'showdown', 'dompurify'), ASA_VERSION . '-' . get_option('asa_cache_bust', time()), true);
        
        // Localize script with settings and translations
        wp_localize_script('asa-script', 'asaSettings', [
            // Plugin configuration
            'systemPrompt' => get_option('asa_system_prompt'),
            'title' => get_option('asa_title'),
            'subtitle' => get_option('asa_subtitle'),
            'primaryColor' => get_option('asa_primary_color', '#333333'),
            'avatar_image_url' => get_option('asa_avatar_image_url'),
            'avatar_icon' => get_option('asa_avatar_icon', 'fas fa-robot'),
            'position' => get_option('asa_position', 'right'),
            'showCredit' => get_option('asa_show_credit', 'yes'),
            
            // API and functionality settings
            'hasApiKey' => ! empty(get_option('asa_api_key')),
            'proactiveMessage' => $this->generate_proactive_message(),
            'proactiveDelay'  => intval(get_option('asa_proactive_delay', 3000)),
            'historyLimit' => intval(get_option('asa_history_limit', 50)),
            
            // AJAX URLs and security
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asa_chat_nonce'),
            'proactiveMessageAjaxUrl' => admin_url('admin-ajax.php?action=asa_generate_proactive_message'),
            
            // Current page context
            'currentPageUrl' => get_permalink(),
            'currentPageTitle' => get_the_title(),
            
            // Translatable strings
            'apiKeyPlaceholder' => esc_attr__('Configure API key in settings', 'asa-ai-sales-agent'),
            'errorMessage' => esc_html__('Sorry, an error occurred: ', 'asa-ai-sales-agent'),
            'noResponseText' => esc_html__('No response received.', 'asa-ai-sales-agent'),
            'serverErrorText' => esc_html__('Sorry, could not communicate with the server. Please try again later.', 'asa-ai-sales-agent'),
            'clearHistoryConfirm' => esc_html__('Are you sure you want to clear the chat history?', 'asa-ai-sales-agent'),
        ]);
    }

    public function generate_proactive_message() {
        // This is a placeholder. The actual proactive message is fetched via AJAX.
        return esc_html__('Hello! How can I help you today?', 'asa-ai-sales-agent');
    }

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_asa-ai-sales-agent') {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('asa-admin-style', plugins_url('css/asa-admin.css', __FILE__), [], ASA_VERSION . '-' . get_option('asa_cache_bust', time()));
        wp_enqueue_style('asa-fa', plugins_url('assets/css/all.min.css', __FILE__), [], ASA_VERSION);
        wp_enqueue_script('asa-admin-script', plugins_url('js/asa-admin.js', __FILE__), array('jquery', 'wp-color-picker', 'media-upload', 'thickbox'), ASA_VERSION . '-' . get_option('asa_cache_bust', time()), true);
        wp_localize_script('asa-admin-script', 'asaAdminSettings', [
            'ajaxUrl'         => admin_url('admin-ajax.php'),
            'nonce'           => wp_create_nonce('asa_settings_nonce'),
            'savingText'      => esc_html__('Saving...', 'asa-ai-sales-agent'),
            'savedText'       => esc_html__('Saved!', 'asa-ai-sales-agent'),
            'errorText'       => esc_html__('Error!', 'asa-ai-sales-agent'),
            'ajaxErrorText'   => esc_html__('AJAX error: ', 'asa-ai-sales-agent'),
            'testingText'     => esc_html__('Testing...', 'asa-ai-sales-agent'),
            'testSuccessText' => esc_html__('Valid API Key!', 'asa-ai-sales-agent'),
            'testErrorText'   => esc_html__('Invalid API Key.', 'asa-ai-sales-agent'),

        ]);
        wp_enqueue_style('thickbox');
    }

    public function register_settings_page() {
        add_options_page(
            'Ai Sales Agent (ASA)',
            esc_html__('Ai Sales Agent (ASA)', 'asa-ai-sales-agent'),
            'manage_options',
            'asa-ai-sales-agent',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        $settings = [
            'asa_api_key'           => 'sanitize_text_field',
            'asa_system_prompt'     => 'sanitize_textarea_field',
            'asa_title'             => 'sanitize_text_field',
            'asa_subtitle'          => 'sanitize_text_field',
            'asa_primary_color'     => 'sanitize_hex_color',
            'asa_avatar_icon'       => 'sanitize_text_field',
            'asa_avatar_image_url'  => 'esc_url_raw',
            'asa_position'          => 'sanitize_text_field',
            'asa_show_credit'       => 'sanitize_text_field',
            'asa_auto_insert'      => 'sanitize_text_field',
            'asa_history_limit'    => 'absint',
            'asa_proactive_delay'  => 'absint',
            'asa_display_types'    => [ $this, 'sanitize_display_types' ]
        ];

        foreach ($settings as $option_name => $sanitize_callback) {
            register_setting('asa_settings_group', $option_name, ['sanitize_callback' => $sanitize_callback]);
        }
    }

    public function asa_save_settings() {
        // Set cache headers to prevent caching of dynamic content
        $this->set_no_cache_headers();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(esc_html__('You do not have sufficient permissions to access this page.', 'asa-ai-sales-agent'));
        }

        check_ajax_referer('asa_settings_nonce', 'security');

        update_option('asa_api_key', sanitize_text_field(wp_unslash($_POST['asa_api_key'] ?? '')));
        update_option('asa_system_prompt', sanitize_textarea_field(wp_unslash($_POST['asa_system_prompt'] ?? '')));
        update_option('asa_title', sanitize_text_field(wp_unslash($_POST['asa_title'] ?? '')));
        update_option('asa_subtitle', sanitize_text_field(wp_unslash($_POST['asa_subtitle'] ?? '')));
        update_option('asa_primary_color', sanitize_hex_color(wp_unslash($_POST['asa_primary_color'] ?? '')));
        update_option('asa_avatar_icon', sanitize_text_field(wp_unslash($_POST['asa_avatar_icon'] ?? '')));
        update_option('asa_avatar_image_url', esc_url_raw(wp_unslash($_POST['asa_avatar_image_url'] ?? '')));
        update_option('asa_position', sanitize_text_field(wp_unslash($_POST['asa_position'] ?? '')));
        update_option('asa_show_credit', sanitize_text_field(wp_unslash($_POST['asa_show_credit'] ?? '')));
        update_option('asa_auto_insert', sanitize_text_field(wp_unslash($_POST['asa_auto_insert'] ?? 'no')));
        update_option('asa_history_limit', absint(wp_unslash($_POST['asa_history_limit'] ?? 50)));
        update_option('asa_proactive_delay', absint(wp_unslash($_POST['asa_proactive_delay'] ?? 3000)));
        $display_types = [];
        if ( isset( $_POST['asa_display_types'] ) ) {
            $display_types = array_map(
                'sanitize_text_field',
                (array) wp_unslash( $_POST['asa_display_types'] )
            );
        }
        update_option( 'asa_display_types', $this->sanitize_display_types( $display_types ) );
        
        // Update cache busting value to force asset refresh
        update_option('asa_cache_bust', time());
        
        // Clear various caches
        $this->clear_plugin_caches();

        wp_send_json_success(esc_html__('Settings saved.', 'asa-ai-sales-agent'));
    }

    public function asa_test_api_key() {
        // Set cache headers to prevent caching of dynamic content
        $this->set_no_cache_headers();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Unauthorized', 'asa-ai-sales-agent')]);
        }

        check_ajax_referer('asa_settings_nonce', 'security');

        $api_key = sanitize_text_field(wp_unslash($_POST['apiKey'] ?? ''));
        if (empty($api_key)) {
            wp_send_json_error(['message' => esc_html__('API key is missing.', 'asa-ai-sales-agent')]);
        }

        $payload = json_encode([
            'contents' => [['role' => 'user', 'parts' => [['text' => 'Hello']]]],
            'system_instruction' => ['parts' => [['text' => 'Say hello']]]
        ]);

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => $payload,
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            $this->log_error('API key test error: ' . $response->get_error_message());
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        $code = wp_remote_retrieve_response_code($response);
        if (200 !== $code) {
            $this->log_error('API key test HTTP status: ' . $code);
            wp_send_json_error(['message' => 'HTTP ' . $code]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['error'])) {
            $this->log_error('API key test API error: ' . $data['error']['message']);
            wp_send_json_error(['message' => $data['error']['message']]);
        }

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            wp_send_json_success(['message' => esc_html__('API Key is valid.', 'asa-ai-sales-agent')]);
        }

        wp_send_json_error(['message' => esc_html__('Unexpected response.', 'asa-ai-sales-agent')]);
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <?php settings_errors(); ?>
            <div class="asa-page-header">
                <h1><?php esc_html_e('ASA ', 'asa-ai-sales-agent'); ?><span class="asa-ai-highlight"><?php esc_html_e('Ai', 'asa-ai-sales-agent'); ?></span><?php esc_html_e(' Sales Agent', 'asa-ai-sales-agent'); ?></h1>
                <div class="asa-header-links">
                    <div class="asa-support-wrapper">
                        <a href="https://buymeacoffee.com/ademisler" target="_blank" class="asa-bmac-button">
                            <i class="fas fa-heart"></i> <?php esc_html_e('Support Development', 'asa-ai-sales-agent'); ?>
                        </a>
                        <span class="asa-support-text"><?php esc_html_e('This plugin is 100% free. If you find it useful, your support helps keep the updates coming!', 'asa-ai-sales-agent'); ?></span>
                    </div>
                </div>
            </div>
            <h2 class="nav-tab-wrapper asa-tabs">
                <a href="#asa-tab-general" class="nav-tab nav-tab-active"><?php esc_html_e('General', 'asa-ai-sales-agent'); ?></a>
                <a href="#asa-tab-appearance" class="nav-tab"><?php esc_html_e('Appearance', 'asa-ai-sales-agent'); ?></a>
                <a href="#asa-tab-behavior" class="nav-tab"><?php esc_html_e('Behavior', 'asa-ai-sales-agent'); ?></a>
            </h2>
            <form id="asa-settings-form" method="post" action="options.php">
                <?php settings_fields('asa_settings_group'); ?>
                <?php do_settings_sections('asa_settings_group'); ?>

                <div id="asa-tab-general" class="asa-tab-content active">
                    <div class="asa-card">
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Gemini API Key', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <div class="asa-api-key-input-group">
                                    <input type="text" name="asa_api_key" id="asa_api_key" value="<?php echo esc_attr(get_option('asa_api_key')); ?>" class="regular-text asa-api-key-input" />
                                     <a href="https://aistudio.google.com/app/apikey" target="_blank" class="button asa-api-key-link-button asa-api-key-button"><?php esc_html_e('Get API Key', 'asa-ai-sales-agent'); ?></a>
                                </div>
                                <div class="asa-api-key-test-group">
                                    <button type="button" class="button" id="asa-test-api-key"><?php esc_html_e('Test API Key', 'asa-ai-sales-agent'); ?></button>
                                    <span id="asa-api-key-test-status"></span>
                                </div>
                                <p class="description"><?php esc_html_e('Enter your Google Gemini API Key here.', 'asa-ai-sales-agent'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="asa-tab-appearance" class="asa-tab-content">
                    <div class="asa-card">
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Title', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="text" name="asa_title" value="<?php echo esc_attr(get_option('asa_title')); ?>" class="regular-text" />
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Subtitle', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="text" name="asa_subtitle" value="<?php echo esc_attr(get_option('asa_subtitle')); ?>" class="regular-text" />
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Primary Color', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="color" name="asa_primary_color" id="asa_primary_color" value="<?php echo esc_attr(get_option('asa_primary_color', '#333333')); ?>" class="asa-color-field" />
                                <p id="asa-color-contrast-warning" class="asa-color-warning" style="display:none;"></p>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Avatar', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <div class="asa-icon-picker-wrapper">
                                    <label><strong><?php esc_html_e('Choose an Icon:', 'asa-ai-sales-agent'); ?></strong></label>
                                    <div class="asa-icon-input-group">
                                        <input type="text" name="asa_avatar_icon" id="asa_avatar_icon" value="<?php echo esc_attr(get_option('asa_avatar_icon', 'fas fa-robot')); ?>" class="regular-text" readonly />
                                        <button type="button" class="button" id="asa-open-icon-picker"><?php esc_html_e('Choose', 'asa-ai-sales-agent'); ?></button>
                                        <div class="asa-icon-preview">
                                            <i class="<?php echo esc_attr(get_option('asa_avatar_icon', 'fas fa-robot')); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                                <hr class="asa-separator">
                                <div class="asa-image-url-wrapper">
                                    <label><strong><?php esc_html_e('Or Use Image URL:', 'asa-ai-sales-agent'); ?></strong></label>
                                    <input type="text" name="asa_avatar_image_url" id="asa_avatar_image_url" value="<?php echo esc_attr(get_option('asa_avatar_image_url')); ?>" class="regular-text" placeholder="https://example.com/avatar.png" />
                                </div>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Position', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <div class="asa-position-selector">
                                    <label>
                                        <input type="radio" name="asa_position" value="left" <?php checked(get_option('asa_position', 'right'), 'left'); ?> />
                                        <div class="asa-position-card">
                                            <div class="asa-position-preview left"></div>
                                            <span><?php esc_html_e('Left', 'asa-ai-sales-agent'); ?></span>
                                        </div>
                                    </label>
                                    <label>
                                        <input type="radio" name="asa_position" value="right" <?php checked(get_option('asa_position', 'right'), 'right'); ?> />
                                        <div class="asa-position-card">
                                            <div class="asa-position-preview right"></div>
                                            <span><?php esc_html_e('Right', 'asa-ai-sales-agent'); ?></span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Show Developer Credit', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="checkbox" name="asa_show_credit" value="yes" <?php checked(get_option('asa_show_credit', 'yes'), 'yes'); ?> />
                            </div>
                        </div>
                    </div>
                </div>

                <div id="asa-tab-behavior" class="asa-tab-content">
                    <div class="asa-card">
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('System Prompt', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <textarea name="asa_system_prompt" class="large-text" rows="5" placeholder="<?php esc_attr_e('You are a helpful sales agent.', 'asa-ai-sales-agent'); ?>"><?php echo esc_textarea(get_option('asa_system_prompt')); ?></textarea>
                                <p class="description"><?php esc_html_e('Define the chatbot\'s personality, role, and response style.', 'asa-ai-sales-agent'); ?></p>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Auto Insert', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="checkbox" name="asa_auto_insert" value="yes" <?php checked(get_option('asa_auto_insert', 'yes'), 'yes'); ?> />
                                <span class="description"><?php esc_html_e('Automatically add chatbot to footer', 'asa-ai-sales-agent'); ?></span>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Display On', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <?php $types = (array)get_option('asa_display_types', ['everywhere']); ?>
                                <label><input type="checkbox" name="asa_display_types[]" value="everywhere" <?php checked(in_array('everywhere', $types), true); ?> /> <?php esc_html_e('Entire Site', 'asa-ai-sales-agent'); ?></label><br />
                                <label><input type="checkbox" name="asa_display_types[]" value="front_page" <?php checked(in_array('front_page', $types), true); ?> /> <?php esc_html_e('Front Page', 'asa-ai-sales-agent'); ?></label><br />
                                <label><input type="checkbox" name="asa_display_types[]" value="posts" <?php checked(in_array('posts', $types), true); ?> /> <?php esc_html_e('Posts', 'asa-ai-sales-agent'); ?></label><br />
                                <label><input type="checkbox" name="asa_display_types[]" value="pages" <?php checked(in_array('pages', $types), true); ?> /> <?php esc_html_e('Pages', 'asa-ai-sales-agent'); ?></label><br />
                                <label><input type="checkbox" name="asa_display_types[]" value="archives" <?php checked(in_array('archives', $types), true); ?> /> <?php esc_html_e('Archives', 'asa-ai-sales-agent'); ?></label>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('History Limit', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="number" name="asa_history_limit" min="1" value="<?php echo esc_attr(get_option('asa_history_limit', 50)); ?>" />
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Proactive Delay (ms)', 'asa-ai-sales-agent'); ?></label>
                            <div class="asa-section-content">
                                <input type="number" name="asa_proactive_delay" min="0" value="<?php echo esc_attr(get_option('asa_proactive_delay', 3000)); ?>" />
                                <p class="description"><?php esc_html_e('Delay before the proactive message appears.', 'asa-ai-sales-agent'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php submit_button(esc_html__('Save Changes', 'asa-ai-sales-agent')); ?>
            </form>
        </div>
        <div id="asa-icon-picker-modal">
            <div class="asa-icon-picker-modal-content">
                <div class="asa-icon-picker-header">
                    <h2><?php esc_html_e('Choose an Icon', 'asa-ai-sales-agent'); ?></h2>
                    <span class="asa-icon-picker-close">&times;</span>
                </div>
                <div class="asa-icon-list"></div>
            </div>
        </div>
        <?php
    }

    public function render_chatbot() {
        ob_start();
        $avatar_image_url = get_option( 'asa_avatar_image_url' );
        $avatar_icon       = get_option( 'asa_avatar_icon', 'fas fa-robot' );
        $avatar_html       = '';
        if ( $avatar_image_url ) {
            $attachment_id = attachment_url_to_postid( $avatar_image_url );
            if ( $attachment_id ) {
                $avatar_html = wp_get_attachment_image(
                    $attachment_id,
                    'full',
                    false,
                    [
                        'class' => 'asa-avatar',
                        'alt'   => esc_attr__( 'Chatbot avatar', 'asa-ai-sales-agent' ),
                    ]
                );
            } else {
                $avatar_html = sprintf(
                    '<span class="asa-avatar asa-avatar-bg" style="background-image:url(%s);" aria-hidden="true"></span>',
                    esc_url( $avatar_image_url )
                );
            }
        } else {
            $avatar_html = '<i class="' . esc_attr( $avatar_icon ) . ' asa-avatar" aria-hidden="true"></i>';
        }
        ?>
        <?php
        $allowed_html = [
            'img' => [
                'src'   => [],
                'class' => [],
            ],
            'i'   => [
                'class' => [],
            ],
        ];
        ?>
        <div id="asa-chatbot" class="asa-position-<?php echo esc_attr(get_option('asa_position', 'right')); ?>" style="--asa-color: <?php echo esc_attr(get_option('asa_primary_color', '#333333')); ?>">
            <div class="asa-launcher" role="button" tabindex="0" aria-haspopup="dialog" aria-expanded="false" aria-label="<?php esc_attr_e('Open chat', 'asa-ai-sales-agent'); ?>">
                <?php echo wp_kses($avatar_html, $allowed_html); ?>
            </div>
            <div class="asa-welcome-wrapper">
                <span class="asa-welcome asa-proactive-message"></span>
                <button class="asa-proactive-close" aria-label="<?php esc_attr_e('Close proactive message', 'asa-ai-sales-agent'); ?>">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="asa-window" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Chat window', 'asa-ai-sales-agent'); ?>" style="display:none;">
                <div class="asa-header">
                    <?php echo wp_kses($avatar_html, $allowed_html); ?>
                    <div class="asa-header-text">
                        <span class="asa-title"><?php echo esc_html(get_option('asa_title', esc_html__('Sales Agent', 'asa-ai-sales-agent'))); ?></span>
                        <span class="asa-subtitle"><?php echo esc_html(get_option('asa_subtitle')); ?></span>
                    </div>
                    <button class="asa-clear-history" title="<?php esc_attr_e('Clear History', 'asa-ai-sales-agent'); ?>" aria-label="<?php esc_attr_e('Clear History', 'asa-ai-sales-agent'); ?>"><i class="fas fa-trash-alt"></i></button>
                    <button class="asa-close" aria-label="<?php esc_attr_e('Close Chat', 'asa-ai-sales-agent'); ?>">&times;</button>
                </div>
                <div class="asa-messages" role="log" aria-live="polite" aria-relevant="additions"></div>
                <div class="asa-typing" style="display:none;"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                <div class="asa-input">
                    <div class="asa-input-wrapper">
                        <input type="text" class="asa-text" placeholder="<?php esc_attr_e('Type your message', 'asa-ai-sales-agent'); ?>" aria-label="<?php esc_attr_e('Message', 'asa-ai-sales-agent'); ?>" <?php if(!get_option('asa_api_key')) echo 'disabled'; ?> />
                        <button class="asa-clear-input" style="display:none;" aria-label="<?php esc_attr_e('Clear Input', 'asa-ai-sales-agent'); ?>"><i class="fas fa-times-circle"></i></button>
                        <button class="asa-send" <?php if(!get_option('asa_api_key')) echo 'disabled'; ?> aria-label="<?php esc_attr_e('Send Message', 'asa-ai-sales-agent'); ?>"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
                <?php if (get_option('asa_show_credit', 'yes') === 'yes'): ?>
                    <div class="asa-credit"><?php esc_html_e('Developed by:', 'asa-ai-sales-agent'); ?> <a href="https://ademisler.com/en" target="_blank" class="ai-name-reveal"><span class="short-name">AI</span><span class="full-name">Adem Isler</span></a></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_chat_request() {
        // Set cache headers to prevent caching of dynamic content
        $this->set_no_cache_headers();
        
        check_ajax_referer('asa_chat_nonce', 'security');

        $api_key = get_option('asa_api_key');
        $message = sanitize_text_field(wp_unslash($_POST['message'] ?? ''));
        
        $raw_history_json = isset( $_POST['history'] ) ? sanitize_textarea_field( wp_unslash( $_POST['history'] ) ) : '[]';
        $decoded_history  = json_decode($raw_history_json, true);
        $history          = $this->sanitize_chat_history($decoded_history);

        $currentPageUrl = esc_url_raw(wp_unslash($_POST['currentPageUrl'] ?? ''));
        $currentPageTitle = sanitize_text_field(wp_unslash($_POST['currentPageTitle'] ?? ''));
        $currentPageContent = sanitize_textarea_field(wp_unslash($_POST['currentPageContent'] ?? ''));

        if (!$api_key || empty($message)) {
            wp_send_json_error(esc_html__('Invalid request', 'asa-ai-sales-agent'));
        }

        $system_prompt = get_option('asa_system_prompt', esc_html__('You are ASA, a friendly and expert sales assistant for this website. Your primary goal is to be proactive, engaging, and helpful. Use the content of the page the user is viewing to understand their interests. Start conversations with insightful questions, highlight product benefits, answer questions clearly, and gently guide them towards making a purchase. Your tone should be persuasive but never pushy. Always aim to provide value and a great customer experience.', 'asa-ai-sales-agent'));
        
        if (!empty($currentPageUrl)) {
            $system_prompt .= "\n\nCurrent Page URL: " . $currentPageUrl;
        }
        if (!empty($currentPageTitle)) {
            $system_prompt .= "\nCurrent Page Title: " . $currentPageTitle;
        }
        if (!empty($currentPageContent)) {
            $currentPageContent = substr($currentPageContent, 0, 4000); 
            $system_prompt .= "\n\nCurrent Page Content: " . $currentPageContent;
        }

        $contents = $history;
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $payload = json_encode([
            'contents' => $contents,
            'system_instruction' => [
                'parts' => [ ['text' => $system_prompt] ]
            ]
        ]);

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $payload,
            'timeout' => 20,
        ]);

        if (is_wp_error($response)) {
            $this->log_error('Chat request error: ' . $response->get_error_message());
            wp_send_json_error(esc_html__('API request failed: ', 'asa-ai-sales-agent') . $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        if (200 !== $code) {
            $this->log_error('Chat request HTTP status: ' . $code);
            wp_send_json_error(esc_html__('HTTP status: ', 'asa-ai-sales-agent') . $code);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            $this->log_error('Chat API error: ' . $data['error']['message']);
            wp_send_json_error(esc_html__('API Error: ', 'asa-ai-sales-agent') . $data['error']['message']);
        }

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $this->log_error('Chat API empty response');
            wp_send_json_error(esc_html__('AI did not return a valid response.', 'asa-ai-sales-agent'));
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'];
        wp_send_json_success($text);
    }

    public function handle_proactive_message_request() {
        // Set cache headers to prevent caching of dynamic content
        $this->set_no_cache_headers();
        
        check_ajax_referer('asa_chat_nonce', 'security');

        $api_key = get_option('asa_api_key');
        if (!$api_key) {
            wp_send_json_error(['message' => esc_html__('API key is not set.', 'asa-ai-sales-agent')]);
        }

        $currentPageUrl = esc_url_raw(wp_unslash($_POST['currentPageUrl'] ?? ''));
        $currentPageContent = sanitize_textarea_field(wp_unslash($_POST['currentPageContent'] ?? ''));

        $cache_key = 'asa_proactive_message_' . md5($currentPageUrl . $currentPageContent);
        $cached_message = get_transient($cache_key);
        if ($cached_message) {
            wp_send_json_success($cached_message);
            return;
        }

        $system_prompt = get_option('asa_system_prompt', esc_html__('You are ASA, a friendly and expert sales assistant for this website. Your primary goal is to be proactive, engaging, and helpful. Use the content of the page the user is viewing to understand their interests. Start conversations with insightful questions, highlight product benefits, answer questions clearly, and gently guide them towards making a purchase. Your tone should be persuasive but never pushy. Always aim to provide value and a great customer experience.', 'asa-ai-sales-agent'));
        $page_content_for_prompt = substr($currentPageContent, 0, 4000);
        $currentPageTitle = sanitize_text_field(wp_unslash($_POST['currentPageTitle'] ?? ''));
        $prompt_instruction = "Generate an extremely short proactive message. It MUST be a single, insightful question of MAXIMUM 5-6 words. Do not use any greetings. Base the question directly on the provided page content. Page Title: {$currentPageTitle}.";

        $payload = json_encode([
            'contents' => [['role' => 'user', 'parts' => [['text' => $page_content_for_prompt]]]],
            'system_instruction' => ['parts' => [['text' => $system_prompt . "\n\n" . $prompt_instruction]]]
        ]);

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => $payload,
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            $this->log_error('Proactive request error: ' . $response->get_error_message());
            wp_send_json_error(['message' => esc_html__('API request failed: ', 'asa-ai-sales-agent') . $response->get_error_message()]);
        }

        $code = wp_remote_retrieve_response_code($response);
        if (200 !== $code) {
            $this->log_error('Proactive request HTTP status: ' . $code);
            wp_send_json_error(['message' => 'HTTP ' . $code]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            $this->log_error('Proactive API error: ' . $data['error']['message']);
            wp_send_json_error(['message' => esc_html__('API Error: ', 'asa-ai-sales-agent') . $data['error']['message']]);
        }

        if ( ! empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            $generated_message = $data['candidates'][0]['content']['parts'][0]['text'];
            
            $words = explode(' ', $generated_message);
            if (count($words) > 7) {
                $generated_message = implode(' ', array_slice($words, 0, 7)) . '...';
            }

            set_transient($cache_key, $generated_message, HOUR_IN_SECONDS);
            wp_send_json_success($generated_message);
        } else {
            // Yanıt boş veya hatalıysa burası çalışacak
            $this->log_error('Proactive API empty response');
            wp_send_json_error(['message' => esc_html__('AI did not return a valid response.', 'asa-ai-sales-agent')]);
        }
    }

    

    public function print_chatbot() {
        echo do_shortcode('[asa_chatbot]');
    }

    public function maybe_print_chatbot() {
        if (get_option('asa_auto_insert', 'yes') !== 'yes') {
            return;
        }

        $types = (array) get_option('asa_display_types', ['everywhere']);
        $show  = in_array('everywhere', $types, true);

        if (!$show) {
            if (in_array('front_page', $types, true) && is_front_page()) {
                $show = true;
            } elseif (in_array('pages', $types, true) && is_page()) {
                $show = true;
            } elseif (in_array('posts', $types, true) && is_single()) {
                $show = true;
            } elseif (in_array('archives', $types, true) && (is_home() || is_archive())) {
                $show = true;
            }
        }

        if ($show) {
            $this->print_chatbot();
        }
    }

    private function log_error( $message ) {
        // This function is intended for debugging purposes only.
        // It is kept empty to pass WordPress.org plugin check standards.
        // To enable logging, you can add: error_log( print_r( $message, true ) );
    }

    /**
     * Set no-cache headers for AJAX responses
     * 
     * Prevents caching of dynamic content by CDNs, proxies, and browsers.
     * Essential for proper functionality with Cloudflare, WP Rocket, etc.
     * 
     * @since 1.0.8
     */
    private function set_no_cache_headers() {
        if (!headers_sent()) {
            // Prevent caching by browsers
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
            
            // Additional headers for CDN compatibility
            header('X-Accel-Expires: 0'); // Nginx
            header('Surrogate-Control: no-store'); // Varnish/Fastly
            header('X-Cache-Control: no-cache'); // CloudFlare
            
            // Prevent proxy caching
            header('Vary: *');
        }
    }

    /**
     * Clear various plugin and WordPress caches
     * 
     * Attempts to clear caches from popular caching plugins and systems
     * to ensure updated settings take effect immediately.
     * 
     * @since 1.0.8
     */
    private function clear_plugin_caches() {
        // Clear WordPress object cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear WP Rocket cache
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        
        // Clear W3 Total Cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        
        // Clear WP Super Cache
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        // Clear LiteSpeed Cache
        if (class_exists('LiteSpeed_Cache_API')) {
            LiteSpeed_Cache_API::purge_all();
        }
        
        // Clear WP Fastest Cache
        if (class_exists('WpFastestCache')) {
            $wpfc = new WpFastestCache();
            $wpfc->deleteCache(true);
        }
        
        // Clear Autoptimize cache
        if (class_exists('autoptimizeCache')) {
            autoptimizeCache::clearall();
        }
        
        // Clear our own transients
        $this->clear_plugin_transients();
    }

    /**
     * Clear plugin-specific transients
     * 
     * @since 1.0.8
     */
    private function clear_plugin_transients() {
        global $wpdb;
        
        // Clear proactive message transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_asa_proactive_message_') . '%'
            )
        );
        
        // Clear timeout transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_timeout_asa_proactive_message_') . '%'
            )
        );
    }

    public function sanitize_display_types( $input ) {
        $allowed = [ 'everywhere', 'front_page', 'posts', 'pages', 'archives' ];
        if ( ! is_array( $input ) ) {
            return [];
        }
        $sanitized = [];
        foreach ( $input as $item ) {
            $item = sanitize_text_field( $item );
            if ( in_array( $item, $allowed, true ) ) {
                $sanitized[] = $item;
            }
        }
        return $sanitized;
    }

    /**
     * Sohbet geçmişi dizisini özyinelemeli olarak temizler.
     *
     * @param array $history_array Temizlenecek sohbet geçmişi.
     * @return array Temizlenmiş sohbet geçmişi.
     */
    private function sanitize_chat_history( $history_array ) {
        if ( ! is_array( $history_array ) ) {
            return [];
        }

        $sanitized_history = [];
        foreach ( $history_array as $entry ) {
            if ( is_array( $entry ) && isset( $entry['role'], $entry['parts'] ) && is_array( $entry['parts'] ) ) {
                $sanitized_entry = [
                    'role' => sanitize_text_field( $entry['role'] ),
                    'parts' => [],
                ];

                foreach($entry['parts'] as $part) {
                    if(is_array($part) && isset($part['text'])) {
                        $sanitized_entry['parts'][] = [ 'text' => sanitize_textarea_field( $part['text'] ) ];
                    }
                }
                if(!empty($sanitized_entry['parts'])) {
                    $sanitized_history[] = $sanitized_entry;
                }
            }
        }
        return $sanitized_history;
    }
}

function asa_activate_plugin() {
    $default_prompt = esc_html__('You are ASA, a friendly and expert sales assistant for this website. Your primary goal is to be proactive, engaging, and helpful. Use the content of the page the user is viewing to understand their interests. Start conversations with insightful questions, highlight product benefits, answer questions clearly, and gently guide them towards making a purchase. Your tone should be persuasive but never pushy. Always aim to provide value and a great customer experience.', 'asa-ai-sales-agent');
    add_option('asa_system_prompt', $default_prompt);
    add_option('asa_title', esc_html__('Sales Agent', 'asa-ai-sales-agent'));
    add_option('asa_subtitle', esc_html__('How can I help you?', 'asa-ai-sales-agent'));
    add_option('asa_primary_color', '#333333');
    add_option('asa_auto_insert', 'yes');
    add_option('asa_display_types', ['everywhere']);
    add_option('asa_history_limit', 50);
    add_option('asa_proactive_delay', 3000);
    
    // Set cache busting value for CDN compatibility
    add_option('asa_cache_bust', time());
}
register_activation_hook(__FILE__, 'asa_activate_plugin');

ASAAISalesAgent::get_instance();

