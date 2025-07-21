<?php
/*
Plugin Name: ASA AI Sales Agent
Description: AI Sales Agent chatbot powered by Google Gemini API.
Version: 0.4.1
Author: Adem İşler
Text Domain: asa
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ASAAISalesAgent {
    private static $instance = null;
    private $default_avatar;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->default_avatar = plugins_url('img/avatar1.svg', __FILE__);
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('asa_chatbot', array($this, 'render_chatbot'));
        add_action('wp_ajax_asa_chat', array($this, 'handle_chat_request'));
        add_action('wp_ajax_nopriv_asa_chat', array($this, 'handle_chat_request'));
        add_action('wp_ajax_asa_save_settings', array($this, 'asa_save_settings'));
        add_action('wp_footer', array($this, 'print_chatbot'));
    }

    public function enqueue_assets() {
        wp_enqueue_style('asa-style', plugins_url('css/asa-style.css', __FILE__));
        wp_enqueue_style('asa-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
        wp_enqueue_script('asa-script', plugins_url('js/asa-script.js', __FILE__), array('jquery'), false, true);
        wp_localize_script('asa-script', 'asaSettings', [
            'apiKey' => get_option('asa_api_key'),
            'systemPrompt' => get_option('asa_system_prompt'),
            'title' => get_option('asa_title'),
            'subtitle' => get_option('asa_subtitle'),
            'primaryColor' => get_option('asa_primary_color', '#0083ff'),
            'avatar_image_url' => get_option('asa_avatar_image_url'),
            'avatar_icon' => get_option('asa_avatar_icon', 'fas fa-robot'),
            'position' => get_option('asa_position', 'right'),
            'showCredit' => get_option('asa_show_credit', 'yes'),
            'proactiveMessage' => $this->generate_proactive_message(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asa_chat_nonce'),
            'currentPageUrl' => get_permalink(),
            'currentPageTitle' => get_the_title(),
            'proactiveMessageAjaxUrl' => admin_url('admin-ajax.php?action=asa_generate_proactive_message'),
        ]);
    }

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_asa-ai-sales-agent') {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('asa-admin-style', plugins_url('css/asa-admin.css', __FILE__));
        wp_enqueue_style('asa-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
        wp_enqueue_script('asa-admin-script', plugins_url('js/asa-admin.js', __FILE__), array('jquery', 'wp-color-picker', 'media-upload', 'thickbox'), false, true);
        wp_localize_script('asa-admin-script', 'asaAdminSettings', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asa_settings_nonce'),
        ]);
        wp_enqueue_style('thickbox');
    }

    public function register_settings_page() {
        add_options_page(
            'ASA AI Sales Agent',
            'ASA AI Sales Agent',
            'manage_options',
            'asa-ai-sales-agent',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('asa_settings_group', 'asa_api_key');
        register_setting('asa_settings_group', 'asa_system_prompt');
        register_setting('asa_settings_group', 'asa_title');
        register_setting('asa_settings_group', 'asa_subtitle');
        register_setting('asa_settings_group', 'asa_primary_color');
        register_setting('asa_settings_group', 'asa_avatar_icon');
        register_setting('asa_settings_group', 'asa_avatar_image_url');
        register_setting('asa_settings_group', 'asa_position');
        register_setting('asa_settings_group', 'asa_show_credit');
    }

    public function asa_save_settings() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('You do not have sufficient permissions to access this page.');
        }

        check_ajax_referer('asa_settings_nonce', 'security');

        // Save settings (example, you might want to loop through $_POST)
        update_option('asa_api_key', sanitize_text_field($_POST['asa_api_key']));
        update_option('asa_system_prompt', sanitize_textarea_field($_POST['asa_system_prompt']));
        update_option('asa_title', sanitize_text_field($_POST['asa_title']));
        update_option('asa_subtitle', sanitize_text_field($_POST['asa_subtitle']));
        update_option('asa_primary_color', sanitize_hex_color($_POST['asa_primary_color']));
        update_option('asa_avatar_icon', sanitize_text_field($_POST['asa_avatar_icon']));
        update_option('asa_avatar_image_url', esc_url_raw($_POST['asa_avatar_image_url']));
        update_option('asa_position', sanitize_text_field($_POST['asa_position']));
        update_option('asa_show_credit', sanitize_text_field($_POST['asa_show_credit']));

        wp_send_json_success('Settings saved.');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('ASA AI Sales Agent', 'asa'); ?></h1>
            <h2 class="nav-tab-wrapper asa-tabs">
                <a href="#asa-tab-general" class="nav-tab nav-tab-active"><?php esc_html_e('General', 'asa'); ?></a>
                <a href="#asa-tab-appearance" class="nav-tab"><?php esc_html_e('Appearance', 'asa'); ?></a>
                <a href="#asa-tab-behavior" class="nav-tab"><?php esc_html_e('Behavior', 'asa'); ?></a>
            </h2>
            <form method="post" action="options.php">
                <?php settings_fields('asa_settings_group'); ?>
                <?php do_settings_sections('asa_settings_group'); ?>

                <div id="asa-tab-general" class="asa-tab-content active">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Gemini API Key', 'asa'); ?></th>
                            <td>
                                <input type="text" name="asa_api_key" value="<?php echo esc_attr(get_option('asa_api_key')); ?>" class="regular-text" />
                                <p class="description"><a href="https://aistudio.google.com/app/apikey" target="_blank"><?php esc_html_e('Get your API key from AI Studio', 'asa'); ?></a></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="asa-tab-appearance" class="asa-tab-content">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Title', 'asa'); ?></th>
                            <td><input type="text" name="asa_title" value="<?php echo esc_attr(get_option('asa_title')); ?>" class="regular-text" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Subtitle', 'asa'); ?></th>
                            <td><input type="text" name="asa_subtitle" value="<?php echo esc_attr(get_option('asa_subtitle')); ?>" class="regular-text" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Primary Color', 'asa'); ?></th>
                            <td><input type="text" name="asa_primary_color" id="asa_primary_color" value="<?php echo esc_attr(get_option('asa_primary_color', '#0083ff')); ?>" class="asa-color-field" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Avatar', 'asa'); ?></th>
                            <td>
                                <div class="asa-icon-picker-wrapper">
                                    <label><strong><?php esc_html_e('Choose an Icon:', 'asa'); ?></strong></label><br>
                                    <input type="text" name="asa_avatar_icon" id="asa_avatar_icon" value="<?php echo esc_attr(get_option('asa_avatar_icon', 'fas fa-robot')); ?>" class="regular-text" readonly />
                                    <button type="button" class="button" id="asa-open-icon-picker"><?php esc_html_e('Choose Icon', 'asa'); ?></button>
                                    <div class="asa-icon-preview">
                                        <i class="<?php echo esc_attr(get_option('asa_avatar_icon', 'fas fa-robot')); ?>"></i>
                                    </div>
                                </div>
                                <hr>
                                <div class="asa-image-url-wrapper">
                                    <label><strong><?php esc_html_e('Or use Image URL:', 'asa'); ?></strong></label><br>
                                    <input type="text" name="asa_avatar_image_url" id="asa_avatar_image_url" value="<?php echo esc_attr(get_option('asa_avatar_image_url')); ?>" class="regular-text" placeholder="https://example.com/avatar.png" />
                                    <p class="description"><?php esc_html_e('Paste an image URL. If this is filled, it will be used instead of the icon.', 'asa'); ?></p>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Position', 'asa'); ?></th>
                            <td>
                                <fieldset>
                                    <label><input type="radio" name="asa_position" value="left" <?php checked(get_option('asa_position'), 'left'); ?> /> <?php esc_html_e('Left', 'asa'); ?></label><br/>
                                    <label><input type="radio" name="asa_position" value="right" <?php checked(get_option('asa_position', 'right'), 'right'); ?> /> <?php esc_html_e('Right', 'asa'); ?></label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Show Developer Credit', 'asa'); ?></th>
                            <td><input type="checkbox" name="asa_show_credit" value="yes" <?php checked(get_option('asa_show_credit', 'yes'), 'yes'); ?> /></td>
                        </tr>
                    </table>
                </div>

                <div id="asa-tab-behavior" class="asa-tab-content">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('System Prompt', 'asa'); ?> <span class="asa-tooltip-icon" data-tooltip="<?php esc_attr_e('Define the chatbot\'s personality, role, and response style. This guides how the AI interacts with users.', 'asa'); ?>">?</span></th>
                            <td><textarea name="asa_system_prompt" class="large-text" rows="5" placeholder="<?php esc_attr_e('You are a helpful sales agent.', 'asa'); ?>"><?php echo esc_textarea(get_option('asa_system_prompt')); ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(esc_html__('Save Changes', 'asa')); ?>
            </form>
            <div class="asa-footer-links">
                <p><a href="https://buymeacoffee.com/ademisler" target="_blank" class="asa-buy-me-a-coffee"><i class="fas fa-coffee"></i> <?php esc_html_e('Support the Developer', 'asa'); ?></a> | <a href="https://ademisler.com/iletisim" target="_blank"><?php esc_html_e('Contact Us', 'asa'); ?></a></p>
            </div>
        </div>
        <div id="asa-icon-picker-modal">
            <div class="asa-icon-picker-modal-content">
                <div class="asa-icon-picker-header">
                    <h2><?php esc_html_e('Choose an Icon', 'asa'); ?></h2>
                    <span class="asa-icon-picker-close">&times;</span>
                </div>
                <input type="text" id="asa-icon-search" placeholder="<?php esc_attr_e('Search for icons...', 'asa'); ?>">
                <div class="asa-icon-list"></div>
            </div>
        </div>
        <?php
    }

    public function render_chatbot() {
        ob_start();
        $avatar_image_url = get_option('asa_avatar_image_url');
        $avatar_icon = get_option('asa_avatar_icon', 'fas fa-robot');
        $avatar_html = $avatar_image_url 
            ? '<img src="' . esc_url($avatar_image_url) . '" class="asa-avatar" />' 
            : '<i class="' . esc_attr($avatar_icon) . ' asa-avatar"></i>';
        ?>
        <div id="asa-chatbot" class="asa-position-<?php echo esc_attr(get_option('asa_position', 'right')); ?>" style="--asa-color: <?php echo esc_attr(get_option('asa_primary_color', '#0083ff')); ?>">
            <div class="asa-launcher">
                <?php echo $avatar_html; ?>
            </div>
            <div class="asa-welcome-wrapper active"><span class="asa-welcome asa-proactive-message"></span><button class="asa-proactive-close">&times;</button></div>
            <div class="asa-window" style="display:none;">
                <div class="asa-header">
                    <?php echo $avatar_html; ?>
                    <div class="asa-header-text">
                        <span class="asa-title"><?php echo esc_html(get_option('asa_title', 'Sales Agent')); ?></span>
                        <span class="asa-subtitle"><?php echo esc_html(get_option('asa_subtitle')); ?></span>
                    </div>
                    <button class="asa-close">&times;</button>
                </div>
                <div class="asa-messages"></div>
                <div class="asa-typing" style="display:none;"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                <div class="asa-input">
                    <input type="text" class="asa-text" placeholder="Type your message" <?php if(!get_option('asa_api_key')) echo 'disabled'; ?> />
                    <button class="asa-clear-input" style="display:none;"><i class="fas fa-times-circle"></i></button>
                    <button class="asa-send" <?php if(!get_option('asa_api_key')) echo 'disabled'; ?>><i class="fas fa-paper-plane"></i></button>
                </div>
                <?php if (get_option('asa_show_credit', 'yes') === 'yes'): ?>
                    <div class="asa-credit">Developed by: <a href="https://ademisler.com" target="_blank">Adem İşler</a></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_chat_request() {
        check_ajax_referer('asa_chat_nonce', 'security');

        $api_key = get_option('asa_api_key');
        $message = sanitize_text_field($_POST['message'] ?? '');
        $history = json_decode(stripslashes($_POST['history'] ?? '[]'), true);
        $currentPageUrl = esc_url_raw($_POST['currentPageUrl'] ?? '');
        $currentPageTitle = sanitize_text_field($_POST['currentPageTitle'] ?? '');
        $currentPageContent = sanitize_textarea_field($_POST['currentPageContent'] ?? '');

        if (!$api_key || empty($message)) {
            wp_send_json_error('Invalid request');
        }

        $system_prompt = get_option('asa_system_prompt', 'You are a helpful sales agent.');
        
        // Add page context to the system instruction
        if (!empty($currentPageUrl)) {
            $system_prompt .= "\n\nCurrent Page URL: " . $currentPageUrl;
        }
        if (!empty($currentPageTitle)) {
            $system_prompt .= "\nCurrent Page Title: " . $currentPageTitle;
        }
        // Add page content to the system instruction, truncating if too long
        if (!empty($currentPageContent)) {
            // Limit content to avoid exceeding token limits, adjust as needed
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
            wp_send_json_error('API request failed: ' . $response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            wp_send_json_error('API Error: ' . $data['error']['message']);
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        wp_send_json_success($text);
    }

    public function handle_proactive_message_request() {
        // No nonce check here as it's a public facing request for proactive message
        // You might add a nonce if you want to restrict this, but for a proactive message, it's usually open.

        $api_key = get_option('asa_api_key');

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ASA Proactive Message Debug: API Key - ' . ($api_key ? 'Set' : 'Not Set'));
        }

        if (!$api_key) {
            wp_send_json_success(__('Hello! How can I help you today?', 'asa'));
        }

        // Try to get from cache
        $cache_key = 'asa_proactive_message_' . md5(get_the_ID() . get_option('asa_system_prompt')); // Cache per page and system prompt
        $cached_message = get_transient($cache_key);

        if ($cached_message) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ASA Proactive Message Debug: Message from cache.');
            }
            wp_send_json_success($cached_message);
        }

        $system_prompt = get_option('asa_system_prompt', 'You are a helpful sales agent.');
        $page_content = substr(strip_tags(get_the_content()), 0, 2000);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ASA Proactive Message Debug: System Prompt - ' . $system_prompt);
            error_log('ASA Proactive Message Debug: Page Content (first 500 chars) - ' . substr($page_content, 0, 500));
        }

        // Add instruction for conciseness and strict length control
        $prompt_instruction = "Generate a very concise and short proactive message (max 2-3 sentences, around 100 characters) based on the following page content and your role. Do not ask questions unless explicitly part of your system prompt. Ensure the message is direct and inviting.";

        $payload = json_encode([
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [ ['text' => $page_content] ]
                ]
            ],
            'system_instruction' => [
                'parts' => [ ['text' => $system_prompt . "\n\n" . $prompt_instruction] ]
            ]
        ]);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ASA Proactive Message Debug: Payload - ' . $payload);
        }

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $payload,
            'timeout' => 15,
        ]);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ASA Proactive Message Debug: Raw API Response - ' . print_r($response, true));
        }

        if (is_wp_error($response)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ASA Proactive Message Debug: WP Error - ' . $response->get_error_message());
            }
            wp_send_json_success(__('Hello! How can I help you today?', 'asa'));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ASA Proactive Message Debug: Decoded API Data - ' . print_r($data, true));
        }

        $proactive_message = __('Hello! How can I help you today?', 'asa'); // Default fallback

        if (!empty($data['candidates'][0]['content']['parts'][0]['text'])) {
            $generated_message = $data['candidates'][0]['content']['parts'][0]['text'];
            // Ensure the text is properly UTF-8 encoded
            if (!mb_check_encoding($generated_message, 'UTF-8')) {
                $generated_message = mb_convert_encoding($generated_message, 'UTF-8', mb_detect_encoding($generated_message, 'UTF-8, ISO-8859-1', true));
            }
            // Strict server-side truncation to ensure message is short
            $proactive_message = substr($generated_message, 0, 150); // Truncate to max 150 characters
            $proactive_message = rtrim($proactive_message, ".,; "); // Remove trailing punctuation if truncated
            $proactive_message .= (strlen($generated_message) > 150) ? '...' : ''; // Add ellipsis if truncated

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ASA Proactive Message Debug: Generated Message - ' . $proactive_message);
            }
            // Cache the generated message for 1 hour (3600 seconds)
            set_transient($cache_key, $proactive_message, HOUR_IN_SECONDS);
        }

        wp_send_json_success($proactive_message);
    }

    private function get_avatar_url() {
        $choice = get_option('asa_avatar_choice', 'avatar1');
        if ($choice === 'custom') {
            $custom = get_option('asa_avatar_custom');
            return $custom ? $custom : $this->default_avatar;
        }
        return plugins_url('img/' . $choice . '.svg', __FILE__);
    }

    private function get_icon_class() {
        $choice = get_option('asa_icon_choice');
        if (!$choice) {
            return '';
        }
        if ($choice === 'custom') {
            return get_option('asa_icon_custom', '');
        }
        return $choice;
    }

    public function print_chatbot() {
        echo do_shortcode('[asa_chatbot]');
    }
}

ASAAISalesAgent::get_instance();
?>
