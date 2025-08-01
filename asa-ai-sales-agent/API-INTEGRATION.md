# Google Gemini API Integration Documentation 🤖

This document provides comprehensive technical details about Ai Sales Agent (ASA)'s integration with Google's Gemini API, including implementation details, best practices, and optimization strategies.

## 📋 Overview

Ai Sales Agent (ASA) integrates with Google's Gemini 2.0 Flash model to provide intelligent, contextual chat responses and proactive messaging. The integration is designed for reliability, security, and optimal performance.

### Key Integration Features

- **Gemini 2.0 Flash Model**: Latest and most capable Google AI model
- **Context-Aware Processing**: Page content analysis for relevant responses
- **Secure Communication**: HTTPS-only with proper authentication
- **Error Handling**: Comprehensive error management and fallbacks
- **Rate Limiting**: Respects API quotas and implements graceful degradation
- **Caching**: Intelligent caching to minimize API calls and costs

---

## 🔑 API Authentication & Setup

### Getting API Credentials

**Step 1: Access Google AI Studio**
```
URL: https://aistudio.google.com/app/apikey
Requirements: Google account with API access enabled
```

**Step 2: Create API Key**
```javascript
// API Key Format
AIzaSyC-[39-character-string]

// Example (not real)
AIzaSyC-1234567890abcdefghijklmnopqrstuvwxyz123
```

**Step 3: Configure Permissions**
```
Required Permissions:
- Generative Language API access
- Content generation permissions
- Rate limiting awareness
```

### Security Considerations

**API Key Storage:**
```php
// Stored securely in WordPress options table
update_option('asa_api_key', sanitize_text_field($api_key));

// Never exposed in frontend JavaScript
// Only used in server-side PHP requests
```

**Access Control:**
```php
// Only admin users can configure API keys
if (!current_user_can('manage_options')) {
    wp_send_json_error('Unauthorized');
}

// Nonce verification for all AJAX requests
check_ajax_referer('asa_settings_nonce', 'security');
```

---

## 🌐 API Endpoints & Communication

### Primary Endpoint

**Base URL:**
```
https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent
```

**Authentication:**
```
Method: URL Parameter
Format: ?key={API_KEY}
```

### Request Structure

**Headers:**
```php
$headers = [
    'Content-Type' => 'application/json',
    'User-Agent' => 'ASA-AI-Sales-Agent/' . ASA_VERSION
];
```

**Request Body:**
```json
{
    "contents": [
        {
            "role": "user",
            "parts": [
                {
                    "text": "User message content"
                }
            ]
        }
    ],
    "system_instruction": {
        "parts": [
            {
                "text": "System prompt defining AI behavior"
            }
        ]
    }
}
```

### Response Structure

**Successful Response:**
```json
{
    "candidates": [
        {
            "content": {
                "parts": [
                    {
                        "text": "AI generated response"
                    }
                ],
                "role": "model"
            },
            "finishReason": "STOP",
            "index": 0,
            "safetyRatings": [...]
        }
    ]
}
```

**Error Response:**
```json
{
    "error": {
        "code": 400,
        "message": "API key not valid. Please pass a valid API key.",
        "status": "INVALID_ARGUMENT"
    }
}
```

---

## 💬 Chat Implementation

### Regular Chat Messages

**PHP Implementation:**
```php
public function handle_chat_request() {
    // Security verification
    check_ajax_referer('asa_chat_nonce', 'security');
    
    // Get and sanitize inputs
    $api_key = get_option('asa_api_key');
    $message = sanitize_text_field(wp_unslash($_POST['message'] ?? ''));
    $history = $this->sanitize_chat_history(
        json_decode(sanitize_textarea_field(wp_unslash($_POST['history'] ?? '[]')), true)
    );
    
    // Build context from current page
    $currentPageUrl = esc_url_raw(wp_unslash($_POST['currentPageUrl'] ?? ''));
    $currentPageTitle = sanitize_text_field(wp_unslash($_POST['currentPageTitle'] ?? ''));
    $currentPageContent = sanitize_textarea_field(wp_unslash($_POST['currentPageContent'] ?? ''));
    
    // Construct system prompt with context
    $system_prompt = get_option('asa_system_prompt', $default_prompt);
    if (!empty($currentPageUrl)) {
        $system_prompt .= "\n\nCurrent Page URL: " . $currentPageUrl;
    }
    if (!empty($currentPageTitle)) {
        $system_prompt .= "\nCurrent Page Title: " . $currentPageTitle;
    }
    if (!empty($currentPageContent)) {
        $currentPageContent = substr($currentPageContent, 0, 4000); // Limit content length
        $system_prompt .= "\n\nCurrent Page Content: " . $currentPageContent;
    }
    
    // Build conversation history
    $contents = $history;
    $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];
    
    // Prepare API payload
    $payload = json_encode([
        'contents' => $contents,
        'system_instruction' => [
            'parts' => [['text' => $system_prompt]]
        ]
    ]);
    
    // Make API request
    $response = wp_remote_post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key,
        [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $payload,
            'timeout' => 20,
        ]
    );
    
    // Handle response
    if (is_wp_error($response)) {
        $this->log_error('Chat request error: ' . $response->get_error_message());
        wp_send_json_error('API request failed: ' . $response->get_error_message());
    }
    
    $code = wp_remote_retrieve_response_code($response);
    if (200 !== $code) {
        $this->log_error('Chat request HTTP status: ' . $code);
        wp_send_json_error('HTTP status: ' . $code);
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (isset($data['error'])) {
        $this->log_error('Chat API error: ' . $data['error']['message']);
        wp_send_json_error('API Error: ' . $data['error']['message']);
    }
    
    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $this->log_error('Chat API empty response');
        wp_send_json_error('AI did not return a valid response.');
    }
    
    $text = $data['candidates'][0]['content']['parts'][0]['text'];
    wp_send_json_success($text);
}
```

### Proactive Message Generation

**Specialized Implementation:**
```php
public function handle_proactive_message_request() {
    check_ajax_referer('asa_chat_nonce', 'security');
    
    $api_key = get_option('asa_api_key');
    if (!$api_key) {
        wp_send_json_error(['message' => 'API key is not set.']);
    }
    
    $currentPageUrl = esc_url_raw(wp_unslash($_POST['currentPageUrl'] ?? ''));
    $currentPageContent = sanitize_textarea_field(wp_unslash($_POST['currentPageContent'] ?? ''));
    
    // Implement caching to reduce API calls
    $cache_key = 'asa_proactive_message_' . md5($currentPageUrl . $currentPageContent);
    $cached_message = get_transient($cache_key);
    if ($cached_message) {
        wp_send_json_success($cached_message);
        return;
    }
    
    $system_prompt = get_option('asa_system_prompt', $default_prompt);
    $page_content_for_prompt = substr($currentPageContent, 0, 4000);
    $currentPageTitle = sanitize_text_field(wp_unslash($_POST['currentPageTitle'] ?? ''));
    
    // Specialized prompt for proactive messages
    $prompt_instruction = "Generate an extremely short proactive message. It MUST be a single, insightful question of MAXIMUM 5-6 words. Do not use any greetings. Base the question directly on the provided page content. Page Title: {$currentPageTitle}.";
    
    $payload = json_encode([
        'contents' => [['role' => 'user', 'parts' => [['text' => $page_content_for_prompt]]]],
        'system_instruction' => ['parts' => [['text' => $system_prompt . "\n\n" . $prompt_instruction]]]
    ]);
    
    $response = wp_remote_post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key,
        [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $payload,
            'timeout' => 15,
        ]
    );
    
    // Similar error handling as regular chat...
    
    if (!empty($data['candidates'][0]['content']['parts'][0]['text'])) {
        $generated_message = $data['candidates'][0]['content']['parts'][0]['text'];
        
        // Enforce length limit
        $words = explode(' ', $generated_message);
        if (count($words) > 7) {
            $generated_message = implode(' ', array_slice($words, 0, 7)) . '...';
        }
        
        // Cache for 1 hour
        set_transient($cache_key, $generated_message, HOUR_IN_SECONDS);
        wp_send_json_success($generated_message);
    } else {
        wp_send_json_error(['message' => 'AI did not return a valid response.']);
    }
}
```

---

## 🔧 Error Handling & Resilience

### Error Types & Responses

**HTTP Status Codes:**
```php
switch ($http_code) {
    case 400:
        // Bad Request - usually API key or payload issues
        $error_message = 'Invalid request format or API key';
        break;
    case 401:
        // Unauthorized - API key issues
        $error_message = 'Invalid or expired API key';
        break;
    case 403:
        // Forbidden - quota or permissions
        $error_message = 'API quota exceeded or access denied';
        break;
    case 429:
        // Too Many Requests - rate limiting
        $error_message = 'Rate limit exceeded, please try again later';
        break;
    case 500:
        // Internal Server Error - Google's side
        $error_message = 'Google API service temporarily unavailable';
        break;
    default:
        $error_message = 'HTTP status: ' . $http_code;
}
```

**API Error Handling:**
```php
if (isset($data['error'])) {
    $error_code = $data['error']['code'] ?? 'unknown';
    $error_message = $data['error']['message'] ?? 'Unknown error';
    
    // Log for debugging
    $this->log_error("API Error {$error_code}: {$error_message}");
    
    // Return user-friendly message
    switch ($error_code) {
        case 400:
            wp_send_json_error('Configuration error. Please check your settings.');
            break;
        case 401:
        case 403:
            wp_send_json_error('API key error. Please verify your API key.');
            break;
        case 429:
            wp_send_json_error('Service busy. Please try again in a moment.');
            break;
        default:
            wp_send_json_error('Service temporarily unavailable.');
    }
}
```

### Fallback Strategies

**Network Failure Handling:**
```php
if (is_wp_error($response)) {
    $error_message = $response->get_error_message();
    
    // Log the specific error
    $this->log_error('Network error: ' . $error_message);
    
    // Provide helpful user message
    if (strpos($error_message, 'timeout') !== false) {
        wp_send_json_error('Request timed out. Please try again.');
    } elseif (strpos($error_message, 'resolve') !== false) {
        wp_send_json_error('Network connection issue. Please check your internet connection.');
    } else {
        wp_send_json_error('Unable to connect to AI service. Please try again later.');
    }
}
```

**Graceful Degradation:**
```javascript
// Frontend fallback handling
.fail(function(xhr, status, error) {
    let errorMessage = 'Sorry, I\'m having trouble connecting right now.';
    
    if (xhr.status === 0) {
        errorMessage = 'Please check your internet connection.';
    } else if (xhr.status >= 500) {
        errorMessage = 'Service temporarily unavailable. Please try again.';
    } else if (xhr.status === 429) {
        errorMessage = 'Too many requests. Please wait a moment.';
    }
    
    renderMessage('bot', errorMessage);
})
```

---

## 📊 Rate Limiting & Quotas

### Google Gemini API Limits

**Free Tier Limits:**
```
Requests per minute: 60
Tokens per minute: 32,000
Requests per day: 1,500
Tokens per day: 50,000
```

**Paid Tier Limits:**
```
Requests per minute: 1,000
Tokens per minute: 128,000
Higher daily limits based on billing
```

### Plugin Rate Limiting Implementation

**Request Throttling:**
```php
// Check for rate limiting headers in response
$rate_limit_remaining = wp_remote_retrieve_header($response, 'x-ratelimit-remaining');
$rate_limit_reset = wp_remote_retrieve_header($response, 'x-ratelimit-reset');

if ($rate_limit_remaining && $rate_limit_remaining < 5) {
    // Warn about approaching rate limit
    $this->log_error('API rate limit approaching: ' . $rate_limit_remaining . ' requests remaining');
}
```

**Client-Side Protection:**
```javascript
// Prevent rapid-fire requests
let lastRequestTime = 0;
const MIN_REQUEST_INTERVAL = 1000; // 1 second

function sendMessage() {
    const now = Date.now();
    if (now - lastRequestTime < MIN_REQUEST_INTERVAL) {
        return; // Ignore rapid requests
    }
    lastRequestTime = now;
    
    // Proceed with request...
}
```

### Cost Optimization

**Token Usage Optimization:**
```php
// Limit page content to reduce token usage
$currentPageContent = substr($currentPageContent, 0, 4000);

// Limit conversation history
if (count($history) > 20) {
    $history = array_slice($history, -20); // Keep last 20 messages
}
```

**Caching Strategy:**
```php
// Cache proactive messages to avoid regeneration
$cache_key = 'asa_proactive_message_' . md5($currentPageUrl . $currentPageContent);
$cached_message = get_transient($cache_key);

if ($cached_message) {
    return $cached_message; // Use cached version
}

// Generate new message and cache for 1 hour
set_transient($cache_key, $generated_message, HOUR_IN_SECONDS);
```

---

## 🛡️ Security Implementation

### Input Sanitization

**User Message Sanitization:**
```php
// Sanitize all user inputs
$message = sanitize_text_field(wp_unslash($_POST['message'] ?? ''));
$currentPageUrl = esc_url_raw(wp_unslash($_POST['currentPageUrl'] ?? ''));
$currentPageTitle = sanitize_text_field(wp_unslash($_POST['currentPageTitle'] ?? ''));
$currentPageContent = sanitize_textarea_field(wp_unslash($_POST['currentPageContent'] ?? ''));
```

**Chat History Sanitization:**
```php
private function sanitize_chat_history($history_array) {
    if (!is_array($history_array)) {
        return [];
    }
    
    $sanitized_history = [];
    foreach ($history_array as $entry) {
        if (is_array($entry) && isset($entry['role'], $entry['parts']) && is_array($entry['parts'])) {
            $sanitized_entry = [
                'role' => sanitize_text_field($entry['role']),
                'parts' => [],
            ];
            
            foreach ($entry['parts'] as $part) {
                if (is_array($part) && isset($part['text'])) {
                    $sanitized_entry['parts'][] = ['text' => sanitize_textarea_field($part['text'])];
                }
            }
            
            if (!empty($sanitized_entry['parts'])) {
                $sanitized_history[] = $sanitized_entry;
            }
        }
    }
    
    return $sanitized_history;
}
```

### Output Sanitization

**Frontend Response Handling:**
```javascript
// Use DOMPurify to sanitize AI responses
if (sender === 'bot' || sender === 'model') {
    const converter = new showdown.Converter({
        simplifiedAutoLink: true,
        strikethrough: true,
        tables: true
    });
    const dirty = converter.makeHtml(text);
    const clean = DOMPurify.sanitize(dirty);
    bubble.html(clean);
} else {
    bubble.text(text); // Plain text for user messages
}
```

### WordPress Security Integration

**Nonce Verification:**
```php
// Verify nonce for all AJAX requests
check_ajax_referer('asa_chat_nonce', 'security');

// Generate nonce in frontend
wp_localize_script('asa-script', 'asaSettings', [
    'nonce' => wp_create_nonce('asa_chat_nonce'),
    // ... other settings
]);
```

**Capability Checks:**
```php
// Ensure only authorized users can modify settings
if (!current_user_can('manage_options')) {
    wp_send_json_error('You do not have sufficient permissions.');
}
```

---

## 🔍 Monitoring & Analytics

### API Usage Tracking

**Basic Usage Logging:**
```php
private function log_api_usage($endpoint, $tokens_used, $response_time) {
    // Log API usage for monitoring
    $usage_data = [
        'timestamp' => current_time('mysql'),
        'endpoint' => $endpoint,
        'tokens' => $tokens_used,
        'response_time' => $response_time,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    // Store in transient for temporary tracking
    $usage_log = get_transient('asa_usage_log') ?: [];
    $usage_log[] = $usage_data;
    
    // Keep only last 100 entries
    if (count($usage_log) > 100) {
        $usage_log = array_slice($usage_log, -100);
    }
    
    set_transient('asa_usage_log', $usage_log, DAY_IN_SECONDS);
}
```

**Error Rate Monitoring:**
```php
private function track_error_rate($error_type) {
    $error_count = get_transient('asa_error_count_' . $error_type) ?: 0;
    $error_count++;
    set_transient('asa_error_count_' . $error_type, $error_count, HOUR_IN_SECONDS);
    
    // Alert if error rate is high
    if ($error_count > 10) {
        $this->log_error("High error rate detected for: {$error_type}");
    }
}
```

### Performance Monitoring

**Response Time Tracking:**
```php
$start_time = microtime(true);

// Make API request...

$end_time = microtime(true);
$response_time = ($end_time - $start_time) * 1000; // Convert to milliseconds

if ($response_time > 5000) { // Alert if response takes more than 5 seconds
    $this->log_error("Slow API response: {$response_time}ms");
}
```

---

## 🚀 Performance Optimization

### Request Optimization

**Payload Minimization:**
```php
// Remove unnecessary data from requests
$optimized_history = array_map(function($entry) {
    return [
        'role' => $entry['role'],
        'parts' => [['text' => substr($entry['parts'][0]['text'], 0, 1000)]] // Limit length
    ];
}, $history);
```

**Connection Reuse:**
```php
// Use persistent connections when possible
$args = [
    'headers' => ['Content-Type' => 'application/json'],
    'body' => $payload,
    'timeout' => 20,
    'httpversion' => '1.1', // Enable keep-alive
];
```

### Caching Strategies

**Proactive Message Caching:**
```php
// Cache based on page content hash
$content_hash = md5($currentPageUrl . $currentPageContent);
$cache_key = "asa_proactive_{$content_hash}";

// Check cache first
$cached_message = get_transient($cache_key);
if ($cached_message) {
    return $cached_message;
}

// Generate and cache for 1 hour
$generated_message = $this->generate_proactive_message($content);
set_transient($cache_key, $generated_message, HOUR_IN_SECONDS);
```

**Response Caching:**
```php
// Cache common responses (be careful with this)
$response_hash = md5($system_prompt . $message);
$cache_key = "asa_response_{$response_hash}";

// Only cache for very common, generic queries
if ($this->is_cacheable_query($message)) {
    $cached_response = get_transient($cache_key);
    if ($cached_response) {
        return $cached_response;
    }
}
```

---

## 🧪 Testing & Development

### API Testing Tools

**Manual Testing:**
```bash
# Test API endpoint directly
curl -X POST \
  'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=YOUR_API_KEY' \
  -H 'Content-Type: application/json' \
  -d '{
    "contents": [
      {
        "role": "user",
        "parts": [
          {
            "text": "Hello, how are you?"
          }
        ]
      }
    ],
    "system_instruction": {
      "parts": [
        {
          "text": "You are a helpful assistant."
        }
      ]
    }
  }'
```

**WordPress Integration Testing:**
```php
// Test function for API connectivity
public function test_api_connection($api_key) {
    $test_payload = json_encode([
        'contents' => [['role' => 'user', 'parts' => [['text' => 'Hello']]]],
        'system_instruction' => ['parts' => [['text' => 'Say hello']]]
    ]);
    
    $response = wp_remote_post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key,
        [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $test_payload,
            'timeout' => 15,
        ]
    );
    
    if (is_wp_error($response)) {
        return ['success' => false, 'message' => $response->get_error_message()];
    }
    
    $code = wp_remote_retrieve_response_code($response);
    if (200 !== $code) {
        return ['success' => false, 'message' => 'HTTP ' . $code];
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!empty($data['error'])) {
        return ['success' => false, 'message' => $data['error']['message']];
    }
    
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        return ['success' => true, 'message' => 'API connection successful'];
    }
    
    return ['success' => false, 'message' => 'Unexpected response format'];
}
```

### Development Environment Setup

**Local Testing:**
```php
// Development mode settings
if (defined('WP_DEBUG') && WP_DEBUG) {
    // Enable verbose logging
    private function log_error($message) {
        error_log('[ASA AI Sales Agent] ' . $message);
    }
    
    // Shorter cache times for testing
    $cache_duration = 60; // 1 minute instead of 1 hour
} else {
    // Production settings
    private function log_error($message) {
        // Silent in production unless critical
        if (strpos($message, 'CRITICAL') === 0) {
            error_log('[ASA AI Sales Agent] ' . $message);
        }
    }
    
    $cache_duration = HOUR_IN_SECONDS;
}
```

---

## 📈 Best Practices & Recommendations

### API Usage Best Practices

1. **Implement Proper Caching**
   - Cache proactive messages for at least 1 hour
   - Consider caching common responses (carefully)
   - Use content-based cache keys

2. **Optimize Token Usage**
   - Limit conversation history length
   - Truncate page content appropriately
   - Use concise system prompts

3. **Handle Errors Gracefully**
   - Provide user-friendly error messages
   - Implement retry logic for transient errors
   - Log errors for debugging

4. **Monitor Usage Patterns**
   - Track API calls and costs
   - Monitor response times
   - Watch for error patterns

5. **Security First**
   - Never expose API keys to frontend
   - Sanitize all inputs and outputs
   - Use WordPress security features

### Performance Optimization

1. **Minimize Request Frequency**
   - Implement request throttling
   - Cache responses when appropriate
   - Batch operations when possible

2. **Optimize Payload Size**
   - Limit conversation history
   - Truncate large content
   - Remove unnecessary data

3. **Use Asynchronous Processing**
   - Non-blocking AJAX requests
   - Background processing for non-critical tasks
   - Progressive enhancement

### Maintenance & Updates

1. **Regular Monitoring**
   - Check API usage and costs
   - Monitor error rates
   - Review performance metrics

2. **Stay Updated**
   - Follow Google AI updates
   - Update to latest API versions
   - Monitor deprecation notices

3. **Documentation**
   - Keep integration docs updated
   - Document custom modifications
   - Maintain change logs

---

This comprehensive API integration documentation should provide developers with all the technical details needed to understand, maintain, and extend the Google Gemini API integration in Ai Sales Agent (ASA). For additional technical support or custom development needs, contact the plugin developer.

## 📞 Support & Resources

- **Google AI Documentation**: [https://ai.google.dev/docs](https://ai.google.dev/docs)
- **Plugin Support**: [WordPress.org Support Forum](https://wordpress.org/support/plugin/asa-ai-sales-agent/)
- **Developer Contact**: [Adem Isler](https://ademisler.com/en)

---

*Last Updated: 2025 - This document reflects the current implementation of Ai Sales Agent (ASA) v1.0.8*