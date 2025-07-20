<?php
/*
Plugin Name: ASA AI Sales Agent
Description: AI Sales Agent chatbot powered by Google Gemini API.
Version: 0.1.0
Author: Adem İşler
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ASAAISalesAgent {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('asa_chatbot', array($this, 'render_chatbot'));
        add_action('wp_ajax_asa_chat', array($this, 'handle_chat_request'));
        add_action('wp_ajax_nopriv_asa_chat', array($this, 'handle_chat_request'));
    }

    public function enqueue_assets() {
        wp_enqueue_style('asa-style', plugins_url('css/asa-style.css', __FILE__));
        wp_enqueue_script('asa-script', plugins_url('js/asa-script.js', __FILE__), array('jquery'), false, true);
        wp_localize_script('asa-script', 'asaSettings', [
            'apiKey' => get_option('asa_api_key'),
            'systemPrompt' => get_option('asa_system_prompt'),
            'title' => get_option('asa_title'),
            'subtitle' => get_option('asa_subtitle'),
            'primaryColor' => get_option('asa_primary_color', '#0083ff'),
            'avatar' => get_option('asa_avatar'),
            'position' => get_option('asa_position', 'right'),
            'showCredit' => get_option('asa_show_credit', 'yes'),
            'proactiveMessage' => $this->generate_proactive_message(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);
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
        register_setting('asa_settings_group', 'asa_avatar');
        register_setting('asa_settings_group', 'asa_position');
        register_setting('asa_settings_group', 'asa_show_credit');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>ASA AI Sales Agent</h1>
            <form method="post" action="options.php">
                <?php settings_fields('asa_settings_group'); ?>
                <?php do_settings_sections('asa_settings_group'); ?>
                <h2>General</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Gemini API Key</th>
                        <td><input type="text" name="asa_api_key" value="<?php echo esc_attr(get_option('asa_api_key')); ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <h2>Appearance</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Title</th>
                        <td><input type="text" name="asa_title" value="<?php echo esc_attr(get_option('asa_title')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Subtitle</th>
                        <td><input type="text" name="asa_subtitle" value="<?php echo esc_attr(get_option('asa_subtitle')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Primary Color</th>
                        <td><input type="text" name="asa_primary_color" value="<?php echo esc_attr(get_option('asa_primary_color', '#0083ff')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Avatar URL</th>
                        <td><input type="text" name="asa_avatar" value="<?php echo esc_attr(get_option('asa_avatar')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Position</th>
                        <td>
                            <select name="asa_position">
                                <option value="left" <?php selected(get_option('asa_position'), 'left'); ?>>Left</option>
                                <option value="right" <?php selected(get_option('asa_position', 'right'), 'right'); ?>>Right</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show Developer Credit</th>
                        <td><input type="checkbox" name="asa_show_credit" value="yes" <?php checked(get_option('asa_show_credit', 'yes'), 'yes'); ?> /></td>
                    </tr>
                </table>
                <h2>Behavior</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">System Prompt</th>
                        <td><textarea name="asa_system_prompt" class="large-text" rows="5"><?php echo esc_textarea(get_option('asa_system_prompt')); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_chatbot() {
        ob_start();
        ?>
        <div id="asa-chatbot" class="asa-position-<?php echo esc_attr(get_option('asa_position', 'right')); ?>" style="--asa-color: <?php echo esc_attr(get_option('asa_primary_color', '#0083ff')); ?>">
            <div class="asa-launcher">
                <img src="<?php echo esc_attr(get_option('asa_avatar')); ?>" class="asa-avatar" />
                <span class="asa-welcome"></span>
            </div>
            <div class="asa-window" style="display:none;">
                <div class="asa-header">
                    <span class="asa-title"><?php echo esc_html(get_option('asa_title', 'Sales Agent')); ?></span>
                    <span class="asa-subtitle"><?php echo esc_html(get_option('asa_subtitle')); ?></span>
                    <button class="asa-close">&times;</button>
                </div>
                <div class="asa-messages"></div>
                <div class="asa-input">
                    <input type="text" class="asa-text" placeholder="Type your message" />
                    <button class="asa-send">Send</button>
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
        $api_key = get_option('asa_api_key');
        $message = sanitize_text_field($_POST['message'] ?? '');
        if (!$api_key || empty($message)) {
            wp_send_json_error('Invalid request');
        }

        $system_prompt = get_option('asa_system_prompt', 'You are a helpful sales agent.');
        $payload = json_encode([
            'model' => 'gemini-2.5-flash',
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $message]
            ]
        ]);

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $payload,
            'timeout' => 20,
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error('Request failed');
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        wp_send_json_success($text);
    }

    private function generate_proactive_message() {
        $api_key = get_option('asa_api_key');
        if (!$api_key) {
            return __('Hello! How can I help you today?', 'asa');
        }

        $system_prompt = get_option('asa_system_prompt', 'You are a helpful sales agent.');
        $page_content = substr(strip_tags(get_the_content()), 0, 2000);

        $payload = json_encode([
            'model' => 'gemini-2.5-flash',
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $page_content]
            ]
        ]);

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $payload,
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return __('Hello! How can I help you today?', 'asa');
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (!empty($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        return __('Hello! How can I help you today?', 'asa');
    }
}

ASAAISalesAgent::get_instance();
?>
