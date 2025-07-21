<?php
/*
Plugin Name: ASA AI Sales Agent
Description: AI Sales Agent chatbot powered by Google Gemini API.

Version: 1.0.0
Author: Adem İşler
Text Domain: asa
Domain Path: /languages

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
        load_plugin_textdomain( 'asa', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        $this->default_avatar = plugins_url('img/avatar1.svg', __FILE__);
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('asa_chatbot', array($this, 'render_chatbot'));
        add_action('wp_ajax_asa_chat', array($this, 'handle_chat_request'));
        add_action('wp_ajax_nopriv_asa_chat', array($this, 'handle_chat_request'));
        add_action('wp_ajax_asa_generate_proactive_message', array($this, 'handle_proactive_message_request'));
        add_action('wp_ajax_nopriv_asa_generate_proactive_message', array($this, 'handle_proactive_message_request'));
        add_action('wp_ajax_asa_save_settings', array($this, 'asa_save_settings'));
        
        
        add_action('wp_footer', array($this, 'print_chatbot'));
    }

    public function enqueue_assets() {
        wp_enqueue_style('asa-style', plugins_url('css/asa-style.css', __FILE__));
        wp_enqueue_style('asa-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
        wp_enqueue_script('showdown', 'https://cdnjs.cloudflare.com/ajax/libs/showdown/2.1.0/showdown.min.js', array(), null, true);
        wp_enqueue_script('asa-script', plugins_url('js/asa-script.js', __FILE__), array('jquery', 'showdown'), false, true);
        wp_localize_script('asa-script', 'asaSettings', [
            'apiKey' => get_option('asa_api_key'),
            'systemPrompt' => get_option('asa_system_prompt'),
            'title' => get_option('asa_title'),
            'subtitle' => get_option('asa_subtitle'),
            'primaryColor' => get_option('asa_primary_color', '#333333'),
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
            'apiKeyPlaceholder' => esc_attr__('Configure API key in settings', 'asa'),
            'errorMessage' => esc_html__('Sorry, an error occurred: ', 'asa'),
            'noResponseText' => esc_html__('No response received.', 'asa'),
            'serverErrorText' => esc_html__('Sorry, could not communicate with the server. Please try again later.', 'asa'),
            'clearHistoryConfirm' => esc_html__('Are you sure you want to clear the chat history?', 'asa'),
        ]);
    }

    public function generate_proactive_message() {
        // This is a placeholder. The actual proactive message is fetched via AJAX.
        return esc_html__('Hello! How can I help you today?', 'asa');
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
            'savingText' => esc_html__('Saving...', 'asa'),
            'savedText' => esc_html__('Saved!', 'asa'),
            'errorText' => esc_html__('Error!', 'asa'),
            
        ]);
        wp_enqueue_style('thickbox');
    }

    public function register_settings_page() {
        add_options_page(
            'ASA AI Sales Agent',
            esc_html__('ASA AI Sales Agent', 'asa'),
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
            wp_send_json_error(esc_html__('You do not have sufficient permissions to access this page.', 'asa'));
        }

        check_ajax_referer('asa_settings_nonce', 'security');

        update_option('asa_api_key', sanitize_text_field($_POST['asa_api_key']));
        update_option('asa_system_prompt', sanitize_textarea_field($_POST['asa_system_prompt']));
        update_option('asa_title', sanitize_text_field($_POST['asa_title']));
        update_option('asa_subtitle', sanitize_text_field($_POST['asa_subtitle']));
        update_option('asa_primary_color', sanitize_hex_color($_POST['asa_primary_color']));
        update_option('asa_avatar_icon', sanitize_text_field($_POST['asa_avatar_icon']));
        update_option('asa_avatar_image_url', esc_url_raw($_POST['asa_avatar_image_url']));
        update_option('asa_position', sanitize_text_field($_POST['asa_position']));
        update_option('asa_show_credit', sanitize_text_field($_POST['asa_show_credit']));

        wp_send_json_success(esc_html__('Settings saved.', 'asa'));
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <div class="asa-page-header">
                <h1><?php esc_html_e('ASA ', 'asa'); ?><span class="asa-ai-highlight"><?php esc_html_e('AI', 'asa'); ?></span><?php esc_html_e(' Sales Agent', 'asa'); ?></h1>
                <div class="asa-header-links">
                    <div class="asa-support-wrapper">
                        <a href="https://buymeacoffee.com/ademisler" target="_blank" class="asa-bmac-button">
                            <i class="fas fa-heart"></i> <?php esc_html_e('Support ASA\'s Development', 'asa'); ?>
                        </a>
                        <span class="asa-support-text"><?php esc_html_e('This plugin is 100% free. If you find it useful, your support helps keep the updates coming!', 'asa'); ?></span>
                    </div>
                </div>
            </div>
            <h2 class="nav-tab-wrapper asa-tabs">
                <a href="#asa-tab-general" class="nav-tab nav-tab-active"><?php esc_html_e('General', 'asa'); ?></a>
                <a href="#asa-tab-appearance" class="nav-tab"><?php esc_html_e('Appearance', 'asa'); ?></a>
                <a href="#asa-tab-behavior" class="nav-tab"><?php esc_html_e('Behavior', 'asa'); ?></a>
            </h2>
            <form id="asa-settings-form" method="post" action="options.php">
                <?php settings_errors(); ?>
                <?php settings_fields('asa_settings_group'); ?>
                <?php do_settings_sections('asa_settings_group'); ?>

                <div id="asa-tab-general" class="asa-tab-content active">
                    <div class="asa-card">
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Gemini API Key', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <div class="asa-api-key-input-group">
                                    <input type="text" name="asa_api_key" id="asa_api_key" value="<?php echo esc_attr(get_option('asa_api_key')); ?>" class="regular-text asa-api-key-input" />
                                    <a href="https://aistudio.google.com/app/apikey" target="_blank" class="button asa-api-key-link-button"><?php esc_html_e('Get API Key', 'asa'); ?></a>
                                </div>
                                <p class="description"><?php esc_html_e('Enter your Google Gemini API Key here.', 'asa'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="asa-tab-appearance" class="asa-tab-content">
                    <div class="asa-card">
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Title', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <input type="text" name="asa_title" value="<?php echo esc_attr(get_option('asa_title')); ?>" class="regular-text" />
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Subtitle', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <input type="text" name="asa_subtitle" value="<?php echo esc_attr(get_option('asa_subtitle')); ?>" class="regular-text" />
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Primary Color', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <input type="text" name="asa_primary_color" id="asa_primary_color" value="<?php echo esc_attr(get_option('asa_primary_color', '#333333')); ?>" class="asa-color-field" />
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Avatar', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <div class="asa-icon-picker-wrapper">
                                    <label><strong><?php esc_html_e('Choose an Icon:', 'asa'); ?></strong></label>
                                    <div class="asa-icon-input-group">
                                        <input type="text" name="asa_avatar_icon" id="asa_avatar_icon" value="<?php echo esc_attr(get_option('asa_avatar_icon', 'fas fa-robot')); ?>" class="regular-text" readonly />
                                        <button type="button" class="button" id="asa-open-icon-picker"><?php esc_html_e('Choose', 'asa'); ?></button>
                                        <div class="asa-icon-preview">
                                            <i class="<?php echo esc_attr(get_option('asa_avatar_icon', 'fas fa-robot')); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                                <hr class="asa-separator">
                                <div class="asa-image-url-wrapper">
                                    <label><strong><?php esc_html_e('Or Use Image URL:', 'asa'); ?></strong></label>
                                    <input type="text" name="asa_avatar_image_url" id="asa_avatar_image_url" value="<?php echo esc_attr(get_option('asa_avatar_image_url')); ?>" class="regular-text" placeholder="https://example.com/avatar.png" />
                                </div>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Position', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <div class="asa-position-selector">
                                    <label>
                                        <input type="radio" name="asa_position" value="left" <?php checked(get_option('asa_position', 'right'), 'left'); ?> />
                                        <div class="asa-position-card">
                                            <div class="asa-position-preview left"></div>
                                            <span><?php esc_html_e('Left', 'asa'); ?></span>
                                        </div>
                                    </label>
                                    <label>
                                        <input type="radio" name="asa_position" value="right" <?php checked(get_option('asa_position', 'right'), 'right'); ?> />
                                        <div class="asa-position-card">
                                            <div class="asa-position-preview right"></div>
                                            <span><?php esc_html_e('Right', 'asa'); ?></span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('Show Developer Credit', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <input type="checkbox" name="asa_show_credit" value="yes" <?php checked(get_option('asa_show_credit', 'yes'), 'yes'); ?> />
                            </div>
                        </div>
                    </div>
                </div>

                <div id="asa-tab-behavior" class="asa-tab-content">
                    <div class="asa-card">
                        <div class="asa-card-section">
                            <label class="asa-section-label"><?php esc_html_e('System Prompt', 'asa'); ?></label>
                            <div class="asa-section-content">
                                <textarea name="asa_system_prompt" class="large-text" rows="5" placeholder="<?php esc_attr_e('You are a helpful sales agent.', 'asa'); ?>"><?php echo esc_textarea(get_option('asa_system_prompt')); ?></textarea>
                                <p class="description"><?php esc_html_e('Define the chatbot\'s personality, role, and response style.', 'asa'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php submit_button(esc_html__('Save Changes', 'asa')); ?>
            </form>
        </div>
        <div id="asa-icon-picker-modal">
            <div class="asa-icon-picker-modal-content">
                <div class="asa-icon-picker-header">
                    <h2><?php esc_html_e('Choose an Icon', 'asa'); ?></h2>
                    <span class="asa-icon-picker-close">&times;</span>
                </div>
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
        <div id="asa-chatbot" class="asa-position-<?php echo esc_attr(get_option('asa_position', 'right')); ?>" style="--asa-color: <?php echo esc_attr(get_option('asa_primary_color', '#333333')); ?>">
            <div class="asa-launcher">
                <?php echo $avatar_html; ?>
            </div>
            <div class="asa-welcome-wrapper"><span class="asa-welcome asa-proactive-message"></span><button class="asa-proactive-close"><i class="fas fa-times"></i></button></div>
            <div class="asa-window" style="display:none;">
                <div class="asa-header">
                    <?php echo $avatar_html; ?>
                    <div class="asa-header-text">
                        <span class="asa-title"><?php echo esc_html(get_option('asa_title', esc_html__('Sales Agent', 'asa'))); ?></span>
                        <span class="asa-subtitle"><?php echo esc_html(get_option('asa_subtitle')); ?></span>
                    </div>
                    <button class="asa-clear-history" title="<?php esc_attr_e('Clear History', 'asa'); ?>" aria-label="<?php esc_attr_e('Clear History', 'asa'); ?>"><i class="fas fa-trash-alt"></i></button>
                    <button class="asa-close" aria-label="<?php esc_attr_e('Close Chat', 'asa'); ?>">&times;</button>
                </div>
                <div class="asa-messages"></div>
                <div class="asa-typing" style="display:none;"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                <div class="asa-input">
                    <div class="asa-input-wrapper">
                        <input type="text" class="asa-text" placeholder="<?php esc_attr_e('Type your message', 'asa'); ?>" <?php if(!get_option('asa_api_key')) echo 'disabled'; ?> />
                        <button class="asa-clear-input" style="display:none;" aria-label="<?php esc_attr_e('Clear Input', 'asa'); ?>"><i class="fas fa-times-circle"></i></button>
                        <button class="asa-send" <?php if(!get_option('asa_api_key')) echo 'disabled'; ?> aria-label="<?php esc_attr_e('Send Message', 'asa'); ?>"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
                <?php if (get_option('asa_show_credit', 'yes') === 'yes'): ?>
                    <div class="asa-credit"><?php esc_html_e('Developed by:', 'asa'); ?> <a href="https://ademisler.com" target="_blank" class="ai-name-reveal"><span class="short-name">AI</span><span class="full-name">Adem Isler</span></a></div>
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
            wp_send_json_error(esc_html__('Invalid request', 'asa'));
        }

        $system_prompt = get_option('asa_system_prompt', esc_html__('You are Salista, a friendly and expert sales assistant for this website. Your primary goal is to be proactive, engaging, and helpful. Use the content of the page the user is viewing to understand their interests. Start conversations with insightful questions, highlight product benefits, answer questions clearly, and gently guide them towards making a purchase. Your tone should be persuasive but never pushy. Always aim to provide value and a great customer experience.', 'asa'));
        
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
            wp_send_json_error(esc_html__('API request failed: ', 'asa') . $response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            wp_send_json_error(esc_html__('API Error: ', 'asa') . $data['error']['message']);
        }

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            wp_send_json_error(esc_html__('AI did not return a valid response.', 'asa'));
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'];
        wp_send_json_success($text);
    }

    public function handle_proactive_message_request() {
        check_ajax_referer('asa_chat_nonce', 'security');

        $api_key = get_option('asa_api_key');
        if (!$api_key) {
            wp_send_json_error(['message' => esc_html__('API key is not set.', 'asa')]);
        }

        $currentPageUrl = esc_url_raw($_POST['currentPageUrl'] ?? '');
        $currentPageContent = sanitize_textarea_field($_POST['currentPageContent'] ?? '');

        $cache_key = 'asa_proactive_message_' . md5($currentPageUrl . $currentPageContent);
        $cached_message = get_transient($cache_key);
        if ($cached_message) {
            wp_send_json_success($cached_message);
            return;
        }

        $system_prompt = get_option('asa_system_prompt', esc_html__('You are Salista, a friendly and expert sales assistant for this website. Your primary goal is to be proactive, engaging, and helpful. Use the content of the page the user is viewing to understand their interests. Start conversations with insightful questions, highlight product benefits, answer questions clearly, and gently guide them towards making a purchase. Your tone should be persuasive but never pushy. Always aim to provide value and a great customer experience.', 'asa'));
        $page_content_for_prompt = substr($currentPageContent, 0, 4000);
        $currentPageTitle = sanitize_text_field($_POST['currentPageTitle'] ?? '');
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
            wp_send_json_error(['message' => esc_html__('API request failed: ', 'asa') . $response->get_error_message()]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            wp_send_json_error(['message' => esc_html__('API Error: ', 'asa') . $data['error']['message']]);
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
            wp_send_json_error(['message' => esc_html__('AI did not return a valid response.', 'asa')]);
        }
    }

    

    public function print_chatbot() {
        echo do_shortcode('[asa_chatbot]');
    }
}

function asa_activate_plugin() {
    $default_prompt = esc_html__('You are ASA, a friendly and expert sales assistant for this website. Your primary goal is to be proactive, engaging, and helpful. Use the content of the page the user is viewing to understand their interests. Start conversations with insightful questions, highlight product benefits, answer questions clearly, and gently guide them towards making a purchase. Your tone should be persuasive but never pushy. Always aim to provide value and a great customer experience.', 'asa');
    add_option('asa_system_prompt', $default_prompt);
}
register_activation_hook(__FILE__, 'asa_activate_plugin');

ASAAISalesAgent::get_instance();
?>
