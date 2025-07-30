# Ai Sales Agent (ASA) 🤖

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-green.svg)](LICENSE)
[![Tested up to](https://img.shields.io/badge/Tested%20up%20to-WordPress%206.4-blue.svg)](https://wordpress.org/)

Transform your website into a sales powerhouse with an intelligent AI chatbot powered by Google Gemini that proactively engages visitors and drives conversions.

## 🌟 Overview

Ai Sales Agent (ASA) is a cutting-edge WordPress plugin that revolutionizes how you engage with website visitors. Unlike traditional chatbots that wait for users to initiate contact, ASA proactively starts conversations based on the content visitors are viewing, creating personalized and contextual interactions that drive engagement and conversions.

### ✨ Key Highlights

- **🧠 Powered by Google Gemini 2.0 Flash** - Latest AI technology for intelligent conversations
- **🎯 Proactive Engagement** - Initiates conversations based on page content
- **📱 Mobile-First Design** - Optimized for all devices with full-screen mobile experience
- **🎨 Fully Customizable** - Match your brand with custom colors, avatars, and positioning
- **🔒 Privacy-Focused** - Local chat storage with transparent data handling
- **♿ Accessibility Ready** - Full WCAG compliance with keyboard navigation
- **🌍 Translation Ready** - Complete internationalization support

## 🚀 Quick Start

### Prerequisites

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Google Gemini API key (free tier available)

### Installation

1. **Download & Install**
   ```bash
       # Via WordPress Admin
    Plugins → Add New → Search "Ai Sales Agent (ASA)" → Install → Activate
   
   # Or upload manually to /wp-content/plugins/asa-ai-sales-agent/
   ```

2. **Get Google Gemini API Key**
   - Visit [Google AI Studio](https://aistudio.google.com/app/apikey)
   - Sign in and create a new API key
   - Copy the generated key

3. **Configure Plugin**
   ```
   WordPress Admin → Settings → Ai Sales Agent (ASA)
   → Paste API key → Test connection → Configure appearance → Save
   ```

4. **Display Chatbot**
   - **Automatic**: Enable auto-insert for selected page types
   - **Manual**: Use shortcode `[asa_chatbot]` anywhere
   - **Template**: Add `<?php echo do_shortcode('[asa_chatbot]'); ?>`

## 📖 Documentation

### Configuration Options

#### General Settings
- **API Key**: Your Google Gemini API key
- **System Prompt**: Customize AI personality and behavior
- **Test Connection**: Verify API key functionality

#### Appearance Settings
- **Title**: Chatbot header text (e.g., "Sales Assistant")
- **Subtitle**: Secondary description text
- **Primary Color**: Brand color for UI elements
- **Avatar**: Choose from 50+ icons or upload custom image
- **Position**: Left or right side placement

#### Behavior Settings
- **Auto Insert**: Enable automatic display
- **Display Types**: Control where chatbot appears
- **Proactive Delay**: Timing for proactive messages (ms)
- **History Limit**: Maximum stored messages per user
- **Show Credit**: Display attribution link

### Shortcode Usage

Basic usage:
```php
[asa_chatbot]
```

In templates:
```php
<?php echo do_shortcode('[asa_chatbot]'); ?>
```

Conditional display:
```php
<?php
if (is_page('contact')) {
    echo do_shortcode('[asa_chatbot]');
}
?>
```

### Display Control

The plugin offers flexible display options:

```php
// Auto-insert options
'everywhere'    // All pages
'front_page'    // Homepage only
'posts'         // Single posts
'pages'         // Static pages
'archives'      // Archive pages
```

## 🛠️ Development

### File Structure

```
asa-ai-sales-agent/
├── asa-ai-sales-agent.php    # Main plugin file
├── readme.txt                # WordPress.org readme
├── README.md                 # This file
├── LICENSE                   # GPL license
├── uninstall.php            # Cleanup script
├── .eslintrc.json           # ESLint configuration
│
├── css/
│   ├── asa-admin.css        # Admin interface styles
│   └── asa-style.css        # Frontend chatbot styles
│
├── js/
│   ├── asa-admin.js         # Admin interface JavaScript
│   └── asa-script.js        # Frontend chatbot functionality
│
├── assets/
│   ├── css/
│   │   └── all.min.css      # FontAwesome icons
│   ├── js/
│   │   ├── showdown.min.js  # Markdown parser
│   │   └── dompurify.min.js # HTML sanitization
│   ├── webfonts/            # FontAwesome fonts
│   └── *.png                # Plugin screenshots/assets
│
└── languages/
    └── asa.pot              # Translation template
```

### Core Classes & Methods

#### Main Plugin Class: `ASAAISalesAgent`

```php
class ASAAISalesAgent {
    // Singleton pattern
    public static function get_instance()
    
    // Asset management
    public function enqueue_assets()
    public function enqueue_admin_assets($hook)
    
    // Settings & admin
    public function register_settings_page()
    public function register_settings()
    public function render_settings_page()
    
    // AJAX handlers
    public function handle_chat_request()
    public function handle_proactive_message_request()
    public function asa_save_settings()
    public function asa_test_api_key()
    
    // Frontend rendering
    public function render_chatbot()
    public function maybe_print_chatbot()
    
    // Utility methods
    private function sanitize_chat_history($history_array)
    private function log_error($message)
}
```

### JavaScript API

#### Frontend (asa-script.js)

```javascript
// Main chatbot functionality
const chatbot = $('#asa-chatbot');

// Key functions
function fetchProactiveMessage()    // Get contextual message
function openChatWindow()          // Display chat interface
function sendMessage()             // Handle user input
function renderMessage()           // Display chat messages
function updateHistoryAndRender()  // Manage chat history
```

#### Admin (asa-admin.js)

```javascript
// Admin interface functionality
function checkContrast()           // Color contrast validation
function showNotice()              // Admin notifications

// Event handlers
$('#asa-settings-form').on('submit')  // Save settings
$('#asa-test-api-key').on('click')    // Test API connection
```

### Hooks & Filters

#### Action Hooks

```php
// Admin hooks
add_action('admin_menu', 'register_settings_page');
add_action('admin_init', 'register_settings');
add_action('admin_enqueue_scripts', 'enqueue_admin_assets');

// Frontend hooks
add_action('wp_enqueue_scripts', 'enqueue_assets');
add_action('wp_footer', 'maybe_print_chatbot');

// AJAX hooks
add_action('wp_ajax_asa_chat', 'handle_chat_request');
add_action('wp_ajax_nopriv_asa_chat', 'handle_chat_request');
add_action('wp_ajax_asa_generate_proactive_message', 'handle_proactive_message_request');
add_action('wp_ajax_nopriv_asa_generate_proactive_message', 'handle_proactive_message_request');
```

#### Available Filters

```php
// Customize system prompt
apply_filters('asa_system_prompt', $prompt, $context);

// Modify API payload
apply_filters('asa_api_payload', $payload, $message, $history);

// Filter proactive message
apply_filters('asa_proactive_message', $message, $page_content);

// Customize display conditions
apply_filters('asa_should_display', $should_display, $context);
```

### API Integration

#### Google Gemini API

The plugin integrates with Google's Gemini 2.0 Flash model:

```php
// API endpoint
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

// Request structure
$payload = [
    'contents' => $conversation_history,
    'system_instruction' => ['parts' => [['text' => $system_prompt]]]
];

// Headers
$headers = [
    'Content-Type' => 'application/json'
];
```

#### Rate Limits & Costs

- **Free Tier**: Generous limits for most websites
- **Paid Usage**: Pay-per-use after free tier
- **Rate Limits**: Handled automatically with proper error responses

## 🔧 Customization

### CSS Customization

Override default styles:

```css
/* Customize chatbot appearance */
#asa-chatbot {
    --asa-color: #your-brand-color;
}

/* Custom launcher styling */
.asa-launcher {
    background: linear-gradient(45deg, #color1, #color2);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

/* Chat window customization */
.asa-window {
    border-radius: 15px;
    backdrop-filter: blur(10px);
}
```

### JavaScript Customization

Extend functionality:

```javascript
// Custom event listeners
$(document).on('asa_chat_opened', function() {
    // Track chat opening
    gtag('event', 'chat_opened');
});

$(document).on('asa_message_sent', function(e, message) {
    // Track user messages
    gtag('event', 'chat_message_sent', {
        'message_length': message.length
    });
});
```

### PHP Customization

```php
// Modify system prompt
add_filter('asa_system_prompt', function($prompt, $context) {
    if ($context === 'product_page') {
        return $prompt . "\nYou are specifically helping with product inquiries.";
    }
    return $prompt;
}, 10, 2);

// Custom display logic
add_filter('asa_should_display', function($should_display, $context) {
    // Hide on specific pages
    if (is_page('privacy-policy')) {
        return false;
    }
    return $should_display;
}, 10, 2);
```

## 🧪 Testing

### API Key Testing

Built-in testing functionality:

```php
// Test API connection
$test_payload = [
    'contents' => [['role' => 'user', 'parts' => [['text' => 'Hello']]]],
    'system_instruction' => ['parts' => [['text' => 'Say hello']]]
];

// Verify response structure
if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    // API key is valid
}
```

### Frontend Testing

Test chatbot functionality:

```javascript
// Test proactive messaging
console.log('Testing proactive message generation...');
fetchProactiveMessage();

// Test chat functionality
console.log('Testing chat submission...');
sendMessage();

// Test history management
console.log('Chat history:', JSON.parse(localStorage.getItem('asa_chat_history')));
```

## 🌍 Internationalization

### Translation Support

The plugin is fully translatable:

```php
// Text domain
load_plugin_textdomain('asa-ai-sales-agent', false, dirname(plugin_basename(__FILE__)) . '/languages/');

// Example usage
esc_html__('Hello! How can I help you today?', 'asa-ai-sales-agent');
```

### Creating Translations

1. Use the included `asa.pot` file
2. Create language-specific `.po` files
3. Compile to `.mo` files
4. Place in `/languages/` directory

Example file structure:
```
languages/
├── asa.pot                    # Template
├── asa-es_ES.po              # Spanish
├── asa-es_ES.mo
├── asa-fr_FR.po              # French
└── asa-fr_FR.mo
```

## 🔒 Security

### Data Handling

- **Input Sanitization**: All user inputs are sanitized using WordPress functions
- **Output Escaping**: All outputs are properly escaped
- **Nonce Verification**: AJAX requests use WordPress nonces
- **Capability Checks**: Admin functions require proper permissions

### Privacy Considerations

- **Local Storage**: Chat history stored in user's browser
- **API Communication**: Messages sent to Google Gemini API
- **Data Retention**: No server-side chat storage
- **Transparency**: Clear privacy notices in documentation

### Security Best Practices

```php
// Input sanitization
$message = sanitize_text_field(wp_unslash($_POST['message'] ?? ''));

// Nonce verification
check_ajax_referer('asa_chat_nonce', 'security');

// Capability checks
if (!current_user_can('manage_options')) {
    wp_send_json_error('Unauthorized');
}

// Output escaping
echo esc_html($user_message);
```

## 🐛 Troubleshooting

### Common Issues

#### API Connection Problems
```bash
# Check API key validity
1. Go to Settings → ASA AI Sales Agent
2. Click "Test API Key"
3. Verify error messages in browser console
```

#### Chatbot Not Appearing
```php
// Check display settings
1. Verify auto-insert is enabled
2. Check page type settings
3. Look for JavaScript errors in console
4. Ensure theme compatibility
```

#### Proactive Messages Not Working
```javascript
// Debug proactive messaging
1. Check browser console for AJAX errors
2. Verify page content is being analyzed
3. Check proactive delay settings
4. Ensure API key has sufficient quota
```

### Debug Mode

Enable debug logging:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Check logs in /wp-content/debug.log
```

### Performance Optimization

```php
// Optimize for high-traffic sites
1. Enable object caching
2. Use CDN for assets
3. Monitor API usage
4. Implement rate limiting if needed
```

## 📊 Analytics & Tracking

### Google Analytics Integration

Track chatbot interactions:

```javascript
// Track chat events
gtag('event', 'chat_interaction', {
    'event_category': 'engagement',
    'event_label': 'chatbot_opened'
});

// Track conversions
gtag('event', 'conversion', {
    'event_category': 'sales',
    'event_label': 'chat_led_conversion'
});
```

### Custom Event Tracking

```javascript
// Dispatch custom events
$(document).trigger('asa_chat_opened');
$(document).trigger('asa_message_sent', [message]);
$(document).trigger('asa_proactive_shown', [proactiveMessage]);
```

## 🤝 Contributing

We welcome contributions! Here's how to get started:

### Development Setup

```bash
# Clone repository
git clone https://github.com/your-repo/asa-ai-sales-agent.git

# Install dependencies (if any)
npm install

# Set up development environment
# Configure local WordPress installation
# Add plugin to wp-content/plugins/
```

### Code Standards

- Follow WordPress Coding Standards
- Use ESLint for JavaScript
- Document all functions and classes
- Include unit tests for new features

### Pull Request Process

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Google Gemini AI** - For providing the AI capabilities
- **FontAwesome** - For the beautiful icons
- **Showdown.js** - For Markdown parsing
- **DOMPurify** - For HTML sanitization
- **WordPress Community** - For the amazing platform

## 📞 Support

- **WordPress Support Forum**: [Plugin Support](https://wordpress.org/support/plugin/asa-ai-sales-agent/)
- **Documentation**: [Plugin Page](https://wordpress.org/plugins/asa-ai-sales-agent/)
- **Developer**: [Adem Isler](https://ademisler.com/en)

## 💝 Support Development

If this plugin helps your business grow, consider [supporting development](https://buymeacoffee.com/ademisler) to keep updates and new features coming!

---

**Transform your website visitors into customers with intelligent, proactive AI engagement. Install Ai Sales Agent (ASA) today and watch your conversions grow!** 🚀